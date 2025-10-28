<?php

use HelgeSverre\Chromadb\Chromadb;
use HelgeSverre\Chromadb\Resources\Collections;
use HelgeSverre\Chromadb\Resources\Items;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ============================
// Items Validation Tests
// ============================

test('Items::add() with empty ids array should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['data' => []], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Empty ids array - should still attempt to send
    $response = $items->add(
        collectionId: 'test-collection-id',
        ids: [],
        embeddings: []
    );

    expect($response->status())->toBe(201);
});

test('Items::add() with empty embeddings array should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['data' => []], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Empty embeddings array - should still attempt to send
    $response = $items->add(
        collectionId: 'test-collection-id',
        ids: ['id1'],
        embeddings: []
    );

    expect($response->status())->toBe(201);
});

test('Items::add() with null parameters should be accepted', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['data' => []], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // null parameters are allowed per method signature
    $response = $items->add(
        collectionId: 'test-collection-id',
        ids: null,
        embeddings: null,
        metadatas: null,
        documents: null,
        uris: null
    );

    expect($response->status())->toBe(201);
});

test('Items::add() with mismatched array lengths (ids vs embeddings) should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['data' => []], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Mismatched array lengths - PHP will still call the method
    // The API server should validate this
    $response = $items->add(
        collectionId: 'test-collection-id',
        ids: ['id1', 'id2', 'id3'],
        embeddings: [[0.1, 0.2], [0.3, 0.4]]  // Only 2 embeddings for 3 ids
    );

    expect($response->status())->toBe(201);
});

test('Items::query() with empty queryEmbeddings array should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make([
            'ids' => [],
            'embeddings' => null,
            'documents' => null,
            'metadatas' => null,
            'distances' => null,
        ], 200),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Empty query embeddings - should still send request
    $response = $items->query(
        collectionId: 'test-collection-id',
        queryEmbeddings: [],
        nResults: 10
    );

    expect($response->status())->toBe(200);
});

test('Items::query() with negative nResults should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make([
            'ids' => [],
            'embeddings' => null,
            'documents' => null,
            'metadatas' => null,
            'distances' => null,
        ], 200),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Negative nResults - should still send request (API should validate)
    $response = $items->query(
        collectionId: 'test-collection-id',
        queryEmbeddings: [[0.1, 0.2]],
        nResults: -5
    );

    expect($response->status())->toBe(200);
});

test('Items::get() with empty ids array should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make([
            'ids' => [],
            'embeddings' => null,
            'documents' => null,
            'metadatas' => null,
        ], 200),
    ]);

    $chromadb->withMockClient($mockClient);

    $items = $chromadb->items();

    // Empty ids array - should still send request
    $response = $items->get(
        collectionId: 'test-collection-id',
        ids: []
    );

    expect($response->status())->toBe(200);
});

// ============================
// Collections Validation Tests
// ============================

test('Collections::create() with empty string name should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['id' => 'empty-name-collection'], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $collections = $chromadb->collections();

    // Empty string name - should still attempt to send
    // The API should validate and reject this
    $response = $collections->create(
        name: '',
        getOrCreate: false
    );

    expect($response->status())->toBe(201);
});

test('Collections::create() with null name should throw TypeError', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['id' => 'test-collection'], 201),
    ]);

    $chromadb->withMockClient($mockClient);

    $collections = $chromadb->collections();

    // Null name violates the string type hint
    // This should throw a TypeError at runtime
    expect(function () use ($collections) {
        $collections->create(
            name: null,  // @phpstan-ignore-line
            getOrCreate: false
        );
    })->toThrow(TypeError::class);
});

test('Collections::update() with malformed UUID should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['id' => 'malformed-uuid-collection'], 200),
    ]);

    $chromadb->withMockClient($mockClient);

    $collections = $chromadb->collections();

    // Malformed UUID - should still attempt to send
    // The API should validate and reject this
    $response = $collections->update(
        collectionId: 'not-a-valid-uuid-!@#$',
        newName: 'updated-name'
    );

    expect($response->status())->toBe(200);
});

test('Collections::getByCrn() with invalid CRN format should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make(['error' => 'Invalid CRN format'], 400),
    ]);

    $chromadb->withMockClient($mockClient);

    $collections = $chromadb->collections();

    // Invalid CRN format - should still attempt to send
    // The API should validate and reject this
    $response = $collections->getByCrn(
        crn: 'invalid-crn-format'
    );

    expect($response->status())->toBe(400);
});

test('Collections::list() with negative offset should handle gracefully', function () {
    $chromadb = new Chromadb(
        token: 'test-token',
        host: 'http://localhost',
        port: '8000'
    );

    $mockClient = new MockClient([
        MockResponse::make([], 200),
    ]);

    $chromadb->withMockClient($mockClient);

    $collections = $chromadb->collections();

    // Negative offset - should still attempt to send
    // The API should validate and reject this
    $response = $collections->list(
        limit: 10,
        offset: -5
    );

    expect($response->status())->toBe(200);
});
