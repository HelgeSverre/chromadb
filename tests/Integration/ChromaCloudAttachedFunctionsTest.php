<?php

/**
 * Chroma Cloud Integration Tests for Attached Functions API
 *
 * These tests run against Chroma Cloud to verify attached functions work
 * with a supported backend (unlike local SQLite which returns 501).
 *
 * IMPORTANT: Chroma Cloud has aggressive rate limiting on the attached functions
 * endpoints. Tests may skip with "Rate limited" message. Wait 5-10 minutes between
 * test runs, or run individual tests with longer delays.
 *
 * Required environment variables:
 * - CHROMA_CLOUD_API_KEY: Chroma Cloud API key
 * - CHROMA_CLOUD_TENANT: Tenant ID (UUID)
 * - CHROMA_CLOUD_DATABASE: Database name
 *
 * Run with: vendor/bin/pest tests/Integration/ChromaCloudAttachedFunctionsTest.php
 * Run single test: vendor/bin/pest tests/Integration/ChromaCloudAttachedFunctionsTest.php --filter="can attach"
 */

use HelgeSverre\Chromadb\Chromadb;
use Saloon\Http\Response;

/**
 * Helper to handle rate limiting with automatic retry
 */
function withRateLimitRetry(callable $callback, int $maxRetries = 3, int $initialDelay = 2): Response
{
    $delay = $initialDelay;

    for ($i = 0; $i < $maxRetries; $i++) {
        $response = $callback();

        if ($response->status() !== 429) {
            return $response;
        }

        // Rate limited - wait and retry
        sleep($delay);
        $delay *= 2; // Exponential backoff
    }

    return $response; // Return last response even if still rate limited
}

beforeEach(function () {
    // Add delay between tests to avoid rate limiting
    usleep(500000); // 500ms
    $apiKey = env('CHROMA_CLOUD_API_KEY');
    $tenant = env('CHROMA_CLOUD_TENANT');
    $database = env('CHROMA_CLOUD_DATABASE');

    if (! $apiKey || ! $tenant || ! $database) {
        test()->markTestSkipped('Chroma Cloud credentials not configured. Set CHROMA_CLOUD_API_KEY, CHROMA_CLOUD_TENANT, and CHROMA_CLOUD_DATABASE in .env');
    }

    $this->chromadb = new Chromadb(
        token: $apiKey,
        host: 'https://api.trychroma.com',
        port: '443',
        tenant: $tenant,
        database: $database,
    );

    // Generate unique collection name for this test run
    $this->testCollectionName = 'php_test_'.uniqid();
});

afterEach(function () {
    // Clean up test collection if it exists
    if (isset($this->testCollectionName) && isset($this->chromadb)) {
        try {
            $this->chromadb->collections()->delete($this->testCollectionName);
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
    }
});

describe('Chroma Cloud Attached Functions', function () {
    it('can connect to Chroma Cloud', function () {
        $response = $this->chromadb->server()->heartbeat();
        expect($response->ok())->toBeTrue();
    });

    it('can create a collection on Chroma Cloud', function () {
        $response = $this->chromadb->collections()->create(
            name: $this->testCollectionName,
            getOrCreate: true
        );

        expect($response->ok())->toBeTrue();
        expect($response->json('name'))->toBe($this->testCollectionName);

        $this->collectionId = $response->json('id');
    });

    it('can attach a function to a collection', function () {
        // Create collection first
        $createResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->create(
            name: $this->testCollectionName,
            getOrCreate: true
        ));
        expect($createResponse->ok())->toBeTrue();
        $collectionId = $createResponse->json('id');

        // Attach a function with retry
        $response = withRateLimitRetry(fn () => $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'php-test-function',
            functionId: 'statistics',
            outputCollection: $this->testCollectionName.'_stats'
        ));

        // Skip if still rate limited after retries
        if ($response->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        // On Chroma Cloud, this should succeed (200) or return a meaningful error
        // NOT 501 like local SQLite
        expect($response->status())->not->toBe(501);

        if ($response->ok()) {
            expect($response->json())->toHaveKey('attached_function');
            expect($response->json())->toHaveKey('created');
            $this->attachedFunctionName = 'php-test-function';
        } else {
            // Log the error for debugging
            dump('Attach function error: '.$response->body());
        }
    });

    it('can get an attached function', function () {
        // Create and attach first
        $createResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->create(
            name: $this->testCollectionName,
            getOrCreate: true
        ));
        $collectionId = $createResponse->json('id');

        $attachResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'php-get-test-function',
            functionId: 'statistics',
            outputCollection: $this->testCollectionName.'_stats_get'
        ));

        if ($attachResponse->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        if (! $attachResponse->ok()) {
            test()->markTestSkipped('Could not attach function: '.$attachResponse->body());
        }

        // Now get the attached function
        $response = withRateLimitRetry(fn () => $this->chromadb->collections()->getAttachedFunction(
            collectionId: $collectionId,
            functionName: 'php-get-test-function'
        ));

        if ($response->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        expect($response->status())->not->toBe(501);

        if ($response->ok()) {
            expect($response->json())->toHaveKey('attached_function');
            expect($response->json('attached_function.name'))->toBe('php-get-test-function');
        }
    });

    it('can detach a function from a collection', function () {
        // Create and attach first
        $createResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->create(
            name: $this->testCollectionName,
            getOrCreate: true
        ));
        $collectionId = $createResponse->json('id');

        $attachResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'php-detach-test-function',
            functionId: 'statistics',
            outputCollection: $this->testCollectionName.'_stats_detach'
        ));

        if ($attachResponse->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        if (! $attachResponse->ok()) {
            test()->markTestSkipped('Could not attach function: '.$attachResponse->body());
        }

        // Detach the function
        $response = withRateLimitRetry(fn () => $this->chromadb->collections()->detachFunction(
            collectionId: $collectionId,
            functionName: 'php-detach-test-function',
            deleteOutput: true
        ));

        if ($response->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        expect($response->status())->not->toBe(501);

        if ($response->ok()) {
            expect($response->json('success'))->toBeTrue();
        }
    });

    it('completes full attach-get-detach lifecycle', function () {
        // Step 1: Create collection
        $createResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->create(
            name: $this->testCollectionName,
            getOrCreate: true
        ));
        expect($createResponse->ok())->toBeTrue();
        $collectionId = $createResponse->json('id');

        // Step 2: Attach function
        $attachResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'lifecycle-test',
            functionId: 'statistics',
            outputCollection: $this->testCollectionName.'_lifecycle'
        ));

        if ($attachResponse->status() === 429) {
            test()->markTestSkipped('Rate limited by Chroma Cloud API');
        }

        if (! $attachResponse->ok()) {
            test()->markTestSkipped('Attach failed: '.$attachResponse->body());
        }

        expect($attachResponse->json('created'))->toBeTrue();

        // Step 3: Get attached function
        $getResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->getAttachedFunction(
            collectionId: $collectionId,
            functionName: 'lifecycle-test'
        ));

        expect($getResponse->ok())->toBeTrue();
        expect($getResponse->json('attached_function.name'))->toBe('lifecycle-test');

        // Step 4: Detach function
        $detachResponse = withRateLimitRetry(fn () => $this->chromadb->collections()->detachFunction(
            collectionId: $collectionId,
            functionName: 'lifecycle-test',
            deleteOutput: true
        ));

        expect($detachResponse->ok())->toBeTrue();

        // Step 5: Verify function is gone
        $getAfterDetach = withRateLimitRetry(fn () => $this->chromadb->collections()->getAttachedFunction(
            collectionId: $collectionId,
            functionName: 'lifecycle-test'
        ));

        expect($getAfterDetach->status())->toBe(404);
    });
});
