<?php

use HelgeSverre\Chromadb\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function createTestVector(float $value = 0.1, int $dimension = 128): array
{
    return array_fill(0, $dimension, $value);
}

// Mock response helpers for embedding tests
function mockOpenAIResponse(int $count = 1, int $dimensions = 1536): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.1 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'text-embedding-3-small',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockVoyageResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.2 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'voyage-3.5',
        'usage' => [
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockMistralResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.3 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'mistral-embed',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockJinaResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.5 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'jina-embeddings-v3',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockOllamaResponse(int $dimensions = 384): array
{
    return [
        'model' => 'all-minilm',
        'embeddings' => [
            createTestVector(0.4, $dimensions),
        ],
    ];
}

function callProtectedMethod($object, $method, ...$args)
{
    $reflection = new ReflectionClass($object);
    $method = $reflection->getMethod($method);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $args);
}

// Request test helpers
function getRequestBody($request)
{
    $reflection = new ReflectionObject($request);
    $method = $reflection->getMethod('defaultBody');
    $method->setAccessible(true);

    return $method->invoke($request);
}

function getRequestQuery($request)
{
    $reflection = new ReflectionObject($request);
    if (! $reflection->hasMethod('defaultQuery')) {
        return [];
    }
    $method = $reflection->getMethod('defaultQuery');
    $method->setAccessible(true);

    return $method->invoke($request);
}

// Version-aware test helpers for ChromaDB version-specific features

/**
 * Get the ChromaDB version from environment variable or by querying the server.
 * Returns a cleaned version string (e.g., "1.3.7" without quotes).
 */
function getChromaVersion(): string
{
    // First check environment variable set by CI
    $envVersion = getenv('CHROMA_VERSION');
    if ($envVersion) {
        return trim($envVersion, '"\'');
    }

    // Fallback to querying the server
    try {
        $chromadb = new HelgeSverre\Chromadb\Chromadb(
            token: env('CHROMADB_TOKEN', 'test-token-chroma-local-dev'),
            host: env('CHROMADB_HOST', 'http://localhost'),
            port: env('CHROMADB_PORT', '8000')
        );

        $version = $chromadb->server()->version();

        return trim($version, '"\'');
    } catch (Exception $e) {
        // If we can't connect, assume a recent version
        return '1.3.7';
    }
}

/**
 * Check if current ChromaDB version meets minimum requirement.
 */
function chromaVersionAtLeast(string $minVersion): bool
{
    $currentVersion = getChromaVersion();

    return version_compare($currentVersion, $minVersion, '>=');
}

/**
 * Skip test if ChromaDB version is below minimum.
 * Call this at the start of version-specific tests.
 */
function skipIfChromaVersionBelow(string $minVersion, string $feature): void
{
    if (! chromaVersionAtLeast($minVersion)) {
        test()->markTestSkipped(
            "{$feature} requires ChromaDB {$minVersion}+. Current version: ".getChromaVersion()
        );
    }
}
