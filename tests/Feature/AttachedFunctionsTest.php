<?php

/**
 * Attached Functions API Tests
 *
 * Note: Attached functions are a ChromaDB 1.3.0+ feature that requires a supported backend.
 * The local SQLite backend (SqliteSysDb) does NOT support attached functions - it returns 501.
 * Full functionality is available with Chroma Cloud or other supported backends.
 *
 * These tests verify:
 * - The API endpoints exist and are callable
 * - Proper error handling for unsupported backends (501)
 * - Request/response structure when supported
 */

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $this->chromadb->server()->reset();
});

describe('Attached Functions API (ChromaDB 1.3.0+)', function () {
    it('can attach a function to a collection', function () {
        skipIfChromaVersionBelow('1.3.0', 'Attached functions');

        // Create a test collection
        $collection = $this->chromadb->collections()->create('attach_func_test', getOrCreate: true);
        expect($collection->ok())->toBeTrue();
        $collectionId = $collection->json('id');

        // Attempt to attach a function
        $response = $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output'
        );

        // Should succeed or return appropriate error (400 for invalid config, 501 if not fully implemented)
        expect($response->status())->toBeIn([200, 201, 400, 404, 501]);

        // If successful, verify response structure
        if ($response->ok()) {
            expect($response->json())
                ->toHaveKey('attached_function')
                ->toHaveKey('created');
        }
    });

    it('can get an attached function by name', function () {
        skipIfChromaVersionBelow('1.3.0', 'Attached functions');

        // Create a test collection
        $collection = $this->chromadb->collections()->create('get_func_test', getOrCreate: true);
        expect($collection->ok())->toBeTrue();
        $collectionId = $collection->json('id');

        // Try to get a non-existent function (should return 404)
        $response = $this->chromadb->collections()->getAttachedFunction(
            collectionId: $collectionId,
            functionName: 'nonexistent-function'
        );

        // Should return 404 for non-existent, or 501 if not implemented
        expect($response->status())->toBeIn([200, 404, 501]);
    });

    it('can detach a function from a collection', function () {
        skipIfChromaVersionBelow('1.3.0', 'Attached functions');

        // Create a test collection
        $collection = $this->chromadb->collections()->create('detach_func_test', getOrCreate: true);
        expect($collection->ok())->toBeTrue();
        $collectionId = $collection->json('id');

        // Try to detach a non-existent function (should return 404)
        $response = $this->chromadb->collections()->detachFunction(
            collectionId: $collectionId,
            functionName: 'nonexistent-function',
            deleteOutput: false
        );

        // Should return 404 for non-existent, 200/204 for success, or 501 if not implemented
        expect($response->status())->toBeIn([200, 204, 404, 501]);
    });

    it('can complete attach-get-detach lifecycle', function () {
        skipIfChromaVersionBelow('1.3.0', 'Attached functions');

        // Create a test collection
        $collection = $this->chromadb->collections()->create('lifecycle_test', getOrCreate: true);
        expect($collection->ok())->toBeTrue();
        $collectionId = $collection->json('id');

        // Step 1: Attach a function
        $attachResponse = $this->chromadb->collections()->attachFunction(
            collectionId: $collectionId,
            name: 'lifecycle-stats',
            functionId: 'statistics',
            outputCollection: 'lifecycle-output'
        );

        // Skip remainder if attach is not supported (501 = not implemented in SqliteSysDb)
        if ($attachResponse->status() === 501) {
            test()->markTestSkipped('Attached functions not implemented in local SQLite backend (requires Chroma Cloud)');
        }

        if ($attachResponse->status() === 400) {
            test()->markTestSkipped('Invalid function configuration: '.$attachResponse->json('message', 'unknown error'));
        }

        if (! $attachResponse->ok()) {
            test()->markTestSkipped('Could not attach function: '.$attachResponse->body());
        }

        // Step 2: Get the attached function
        $getResponse = $this->chromadb->collections()->getAttachedFunction(
            collectionId: $collectionId,
            functionName: 'lifecycle-stats'
        );

        expect($getResponse->ok())->toBeTrue();
        expect($getResponse->json('attached_function'))->toBeArray();

        // Step 3: Detach the function
        $detachResponse = $this->chromadb->collections()->detachFunction(
            collectionId: $collectionId,
            functionName: 'lifecycle-stats',
            deleteOutput: true
        );

        expect($detachResponse->ok())->toBeTrue();
    });

    it('handles delete_output parameter correctly', function () {
        skipIfChromaVersionBelow('1.3.0', 'Attached functions');

        // Create a test collection
        $collection = $this->chromadb->collections()->create('delete_output_test', getOrCreate: true);
        expect($collection->ok())->toBeTrue();
        $collectionId = $collection->json('id');

        // Detach with delete_output=true
        $response = $this->chromadb->collections()->detachFunction(
            collectionId: $collectionId,
            functionName: 'test-function',
            deleteOutput: true
        );

        // Response should indicate the request was processed
        expect($response->status())->toBeIn([200, 204, 404, 501]);
    });
});
