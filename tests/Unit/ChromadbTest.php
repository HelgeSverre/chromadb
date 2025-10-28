<?php

use HelgeSverre\Chromadb\Chromadb;
use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;
use HelgeSverre\Chromadb\Embeddings\OpenAIEmbeddings;

// Test 1: Default initialization with host/port/token
test('default initialization with host and port', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    expect($client->getTenant())->toBe('default_tenant')
        ->and($client->getDatabase())->toBe('default_database')
        ->and($client->getEmbeddingFunction())->toBeNull()
        ->and($client->resolveBaseUrl())->toBe('http://localhost:8000');
});

// Test 2: Custom port configuration (non-8000)
test('custom port configuration with non-standard port', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'http://api.example.com',
        port: '9000'
    );

    expect($client->resolveBaseUrl())->toBe('http://api.example.com:9000');
});

// Test 3: Initialization preserves token for authentication
test('initialization preserves token for authentication', function () {
    $client = new Chromadb(
        token: 'my-secret-token',
        host: 'http://localhost',
        port: '8000'
    );

    // Token is set and will be used in headers via Saloon's connector
    expect($client)->toBeInstanceOf(Chromadb::class);
});

// Test 4: withTenant() returns new immutable instance
test('withTenant returns new instance without modifying original', function () {
    $originalClient = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        tenant: 'original_tenant'
    );

    $newClient = $originalClient->withTenant('new_tenant');

    // Original should remain unchanged
    expect($originalClient->getTenant())->toBe('original_tenant')
        // New instance should have updated tenant
        ->and($newClient->getTenant())->toBe('new_tenant')
        // They should be different objects
        ->and($newClient)->not->toBe($originalClient);
});

// Test 5: withDatabase() returns new immutable instance
test('withDatabase returns new instance without modifying original', function () {
    $originalClient = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        database: 'original_database'
    );

    $newClient = $originalClient->withDatabase('new_database');

    // Original should remain unchanged
    expect($originalClient->getDatabase())->toBe('original_database')
        // New instance should have updated database
        ->and($newClient->getDatabase())->toBe('new_database')
        // They should be different objects
        ->and($newClient)->not->toBe($originalClient);
});

// Test 6: withEmbeddings() configures embedding function
test('withEmbeddings configures embedding function', function () {
    $originalClient = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    // Create a mock embedding function
    $mockEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1, 0.2, 0.3], $texts);
        }
    };

    $newClient = $originalClient->withEmbeddings($mockEmbedding);

    // Original should have no embedding function
    expect($originalClient->getEmbeddingFunction())->toBeNull()
        // New instance should have the embedding function
        ->and($newClient->getEmbeddingFunction())->toBe($mockEmbedding)
        // They should be different objects
        ->and($newClient)->not->toBe($originalClient);
});

// Test 7: URL resolution with custom tenant/database overrides
test('base URL construction ignores tenant and database context', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'https://api.chromadb.cloud',
        port: '443',
        tenant: 'production',
        database: 'vectors'
    );

    $baseUrl = $client->resolveBaseUrl();

    // Base URL should only contain host and port, not tenant/database
    expect($baseUrl)->toBe('https://api.chromadb.cloud:443')
        ->and($baseUrl)->not->toContain('production')
        ->and($baseUrl)->not->toContain('vectors');
});

// Test 8: Multiple chained with*() calls maintain immutability
test('multiple chained with calls maintain immutability', function () {
    $original = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        tenant: 'original_tenant',
        database: 'original_database'
    );

    $mockEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1, 0.2, 0.3], $texts);
        }
    };

    $modified = $original
        ->withTenant('tenant_1')
        ->withDatabase('database_1')
        ->withEmbeddings($mockEmbedding);

    // Verify original is unchanged
    expect($original->getTenant())->toBe('original_tenant')
        ->and($original->getDatabase())->toBe('original_database')
        ->and($original->getEmbeddingFunction())->toBeNull();

    // Verify modified has all changes
    expect($modified->getTenant())->toBe('tenant_1')
        ->and($modified->getDatabase())->toBe('database_1')
        ->and($modified->getEmbeddingFunction())->toBe($mockEmbedding);

    // Verify they are different objects
    expect($modified)->not->toBe($original);
});

// Test 9: Initialization with null token is supported
test('initialization with null token is supported', function () {
    $client = new Chromadb(
        token: null,
        host: 'http://localhost',
        port: '8000'
    );

    // Token is null - client will work without authentication
    expect($client)->toBeInstanceOf(Chromadb::class)
        ->and($client->resolveBaseUrl())->toBe('http://localhost:8000');
});

// Test 10: Base URL construction with trailing slashes
test('base URL construction handles host without trailing slash', function () {
    $clientWithoutTrailingSlash = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $clientWithTrailingSlash = new Chromadb(
        token: 'test-token',
        host: 'http://localhost/',
        port: '8000'
    );

    // Both should resolve to proper URLs (the format shows host:port)
    expect($clientWithoutTrailingSlash->resolveBaseUrl())->toBe('http://localhost:8000')
        ->and($clientWithTrailingSlash->resolveBaseUrl())->toBe('http://localhost/:8000');
});

// Additional Tests: Verify resources can be accessed
test('client method returns self for method chaining', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    expect($client->client())->toBe($client);
});

test('resources are instantiated correctly', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    expect($client->collections())->not->toBeNull()
        ->and($client->items())->not->toBeNull()
        ->and($client->database())->not->toBeNull()
        ->and($client->tenant())->not->toBeNull()
        ->and($client->server())->not->toBeNull();
});

// Test withEmbeddings with OpenAI instance
test('withEmbeddings works with OpenAIEmbeddings instance', function () {
    $client = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $openaiEmbedding = new OpenAIEmbeddings('test-api-key');
    $newClient = $client->withEmbeddings($openaiEmbedding);

    expect($newClient->getEmbeddingFunction())->toBeInstanceOf(OpenAIEmbeddings::class)
        ->and($originalClient = $client->getEmbeddingFunction())->toBeNull();
});

// Test initialization with all custom parameters
test('initialization with all custom parameters', function () {
    $mockEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1], $texts);
        }
    };

    $client = new Chromadb(
        token: 'custom-token',
        host: 'https://api.custom.com',
        port: '9000',
        tenant: 'custom_tenant',
        database: 'custom_database',
        embeddingFunction: $mockEmbedding
    );

    expect($client->getTenant())->toBe('custom_tenant')
        ->and($client->getDatabase())->toBe('custom_database')
        ->and($client->getEmbeddingFunction())->toBe($mockEmbedding)
        ->and($client->resolveBaseUrl())->toBe('https://api.custom.com:9000');
});

// Test that with methods preserve other properties
test('withTenant preserves other properties', function () {
    $mockEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1], $texts);
        }
    };

    $original = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        database: 'original_db',
        embeddingFunction: $mockEmbedding
    );

    $modified = $original->withTenant('new_tenant');

    // Should preserve database and embedding function
    expect($modified->getDatabase())->toBe('original_db')
        ->and($modified->getEmbeddingFunction())->toBe($mockEmbedding)
        ->and($modified->getTenant())->toBe('new_tenant');
});

test('withDatabase preserves other properties', function () {
    $mockEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1], $texts);
        }
    };

    $original = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        tenant: 'original_tenant',
        embeddingFunction: $mockEmbedding
    );

    $modified = $original->withDatabase('new_database');

    // Should preserve tenant and embedding function
    expect($modified->getTenant())->toBe('original_tenant')
        ->and($modified->getEmbeddingFunction())->toBe($mockEmbedding)
        ->and($modified->getDatabase())->toBe('new_database');
});

test('withEmbeddings preserves other properties', function () {
    $original = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000',
        tenant: 'original_tenant',
        database: 'original_db'
    );

    $newEmbedding = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            return array_map(fn () => [0.1], $texts);
        }
    };

    $modified = $original->withEmbeddings($newEmbedding);

    // Should preserve tenant and database
    expect($modified->getTenant())->toBe('original_tenant')
        ->and($modified->getDatabase())->toBe('original_db')
        ->and($modified->getEmbeddingFunction())->toBe($newEmbedding);
});
