<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    // Explicit reset for ItemTest to ensure clean state
    $this->chromadb->server()->reset();
});

it('adds items with ids and metadatas  correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
        ],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
        ],
        metadatas: [
            ['title' => 'metadata1'],
            ['title' => 'metadata2'],
        ],
    );

    expect($addItemsResponse->successful())->toBeTrue()
        ->and($addItemsResponse->status())->toEqual(201);
});

it('adds items with ids, documents and embeddings correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
        ],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
        ],
        documents: [
            'text for item 1',
            'text for item 2',
        ],
    );

    expect($addItemsResponse->successful())->toBeTrue()
        ->and($addItemsResponse->status())->toEqual(201);
});

it('adds items with ids and documents correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
        ],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
        ],
        documents: [
            'text for item 1',
            'text for item 2',
        ],
    );

    expect($addItemsResponse->successful())->toBeTrue()
        ->and($addItemsResponse->status())->toEqual(201);
});

it('adds items with ids, metadata and documents correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
        ],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
        ],
        metadatas: [
            ['title' => 'metadata1'],
            ['title' => 'metadata2'],
        ],
        documents: [
            'text for item 1',
            'text for item 2',
        ],
    );

    expect($addItemsResponse->successful())->toBeTrue()
        ->and($addItemsResponse->status())->toEqual(201);
});

it('counts items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: [createTestVector(0.1)],
        metadatas: [['title' => 'metadata1']],
        documents: ['document1']
    );

    // Count items
    $itemCount = $this->chromadb->items()->count(collectionId: $collectionId);

    expect($itemCount)->toEqual(1);

    // Add items (v2 API requires embeddings)
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item2'],
        embeddings: [createTestVector(0.2)],
        metadatas: [['title' => 'metadata2']],
        documents: ['document2']
    );

    // Count items again
    $itemCount = $this->chromadb->items()->count(collectionId: $collectionId);

    expect($itemCount)->toEqual(2);
});

it('retrieves items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $res = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
            createTestVector(0.3),
        ],
        documents: ['document1', 'document2', 'document3']
    );

    // Retrieve items
    $getItemsResponse = $this->chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['item1', 'item3'],
        include: ['documents']
    );

    expect($getItemsResponse->ok())->toBeTrue()
        ->and($getItemsResponse->json('ids'))->toHaveCount(2)
        ->and($getItemsResponse->json('ids'))->toEqual([
            'item1',
            'item3',
        ])
        ->and($getItemsResponse->json('documents'))->toHaveCount(2)
        ->and($getItemsResponse->json('documents'))->toEqual([
            'document1',
            'document3',
        ]);
});

it('updates items with ids, documents and metadatas correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();

    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item'],
        embeddings: [createTestVector(0.1)],
        metadatas: [['title' => 'metadata']],
        documents: ['document']
    );

    // Update items
    $updateItemsResponse = $this->chromadb->items()->update(
        collectionId: $collectionId,
        ids: ['item'],
        metadatas: [['title' => 'updated_metadata']],
        documents: ['updated_document']
    );

    expect($updateItemsResponse->successful())->toBeTrue();
});

it('updates documents with ids, embeddings and metadatas correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();

    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item'],
        embeddings: [createTestVector(0.1)],
        metadatas: [['title' => 'metadata']],
        documents: ['document']
    );

    // Update items
    $updateItemsResponse = $this->chromadb->items()->update(
        collectionId: $collectionId,
        ids: ['item'],
        metadatas: [['title' => 'updated_metadata']],
        documents: ['updated_document']
    );

    expect($updateItemsResponse->successful())->toBeTrue();
});

it('upserts items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Upsert items (v2 API requires embeddings)
    $upsertItemsResponse = $this->chromadb->items()->upsert(
        collectionId: $collectionId,
        ids: ['item'],
        embeddings: [createTestVector(0.1)],
        metadatas: [['title' => 'metadata']],
        documents: ['document']
    );

    expect($upsertItemsResponse->ok())->toBeTrue();
});

it('queries items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.9),
        ],
        metadatas: [
            ['title' => 'metadata1'],
            ['title' => 'metadata2'],
        ],
        documents: ['document1', 'document2']
    );

    // Query items
    $queryItemsResponse = $this->chromadb->items()->query(
        collectionId: $collectionId,
        queryEmbeddings: [createTestVector(0.8)],
        include: ['documents', 'metadatas', 'distances'],
        nResults: 2
    );

    expect($queryItemsResponse->ok())->toBeTrue()
        ->and($queryItemsResponse->json('ids.0'))->toHaveCount(2)
        ->and($queryItemsResponse->json('distances.0'))->toHaveCount(2)
        ->and($queryItemsResponse->json('documents.0'))->toHaveCount(2);
});

it('deletes items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items', getOrCreate: true);
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items (v2 API requires embeddings)
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
        ],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
        ],
        documents: [
            'text for item 1',
            'text for item 2',
        ],
    );

    // Delete items
    $deleteItemsResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        ids: ['item1', 'item2']
    );

    // In v2 API, delete returns an empty array
    expect($deleteItemsResponse->ok())->toBeTrue()
        ->and($deleteItemsResponse->json())->toEqual([]);

    // Verify deletion by counting
    $countAfterDelete = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countAfterDelete)->toEqual(0);
});

it('performs hybrid search correctly', function () {
    // NOTE: Search endpoint is not implemented in ChromaDB v1.0.0 (returns 501 Not Implemented)
    // This test documents the expected behavior for when it becomes available

    // Setup collection with items
    $collection = $this->chromadb->collections()->create('search_test_collection', getOrCreate: true);
    $collectionId = $collection->json('id');

    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.5),
            createTestVector(0.9),
        ],
        metadatas: [
            ['category' => 'A'],
            ['category' => 'B'],
            ['category' => 'A'],
        ],
        documents: ['doc1', 'doc2', 'doc3']
    );

    // Perform hybrid search with filter and select
    $response = $this->chromadb->items()->search(
        collectionId: $collectionId,
        searches: [[
            'filter' => [
                'query_ids' => ['item1', 'item3'],
            ],
            'limit' => [
                'limit' => 10,
                'offset' => 0,
            ],
            'select' => [
                'keys' => ['Document', 'Metadata'],
            ],
        ]]
    );

    // Search endpoint returns 501 Not Implemented in v1.0.0
    // When implemented, it should return 200 with search results
    expect($response->status())->toBeIn([200, 501]);
})->skip('Search endpoint not implemented in ChromaDB v1.0.0 - returns 501');

it('adds items with uris correctly', function () {
    $collection = $this->chromadb->collections()->create('test_uris_collection', getOrCreate: true);
    $collectionId = $collection->json('id');

    $response = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: [createTestVector(0.1)],
        uris: ['https://example.com/doc1']
    );

    // Add returns 201 Created, not 200
    expect($response->status())->toEqual(201);

    $getResponse = $this->chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['item1'],
        include: ['uris']
    );

    expect($getResponse->ok())->toBeTrue()
        ->and($getResponse->json('uris'))->toEqual(['https://example.com/doc1']);
});
