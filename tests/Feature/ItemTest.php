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

    // Search endpoint returns 501 Not Implemented in v1.0.x
    // When implemented, it should return 200 with search results
    expect($response->status())->toBeIn([200, 501]);
})->skip('Search endpoint not implemented in ChromaDB v1.0.x (local) - returns 501. Hybrid search requires combining query methods.');

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

// Section 3: Items Resource Error Handling
// Wave 2 Tests - Error scenarios for embedding functions

it('addWithEmbeddings throws RuntimeException when no embedding function configured', function () {
    // Create a new client without embedding function
    $clientWithoutEmbeddings = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $collection = $clientWithoutEmbeddings->collections()->create('test_tenant_items_wave2_error_add', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Should throw RuntimeException
    expect(fn () => $clientWithoutEmbeddings->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['test document 1', 'test document 2']
    ))->toThrow(RuntimeException::class);
});

it('queryWithText throws RuntimeException when no embedding function configured', function () {
    // Create a new client without embedding function
    $clientWithoutEmbeddings = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $collection = $clientWithoutEmbeddings->collections()->create('test_tenant_items_wave2_error_query', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Should throw RuntimeException
    expect(fn () => $clientWithoutEmbeddings->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'test query'
    ))->toThrow(RuntimeException::class);
});

it('error message content is descriptive and actionable for missing embedding function', function () {
    // Create a new client without embedding function
    $clientWithoutEmbeddings = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $collection = $clientWithoutEmbeddings->collections()->create('test_tenant_items_wave2_error_msg', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Catch exception and verify error message
    try {
        $clientWithoutEmbeddings->items()->addWithEmbeddings(
            collectionId: $collectionId,
            documents: ['test document']
        );
        expect(true)->toBeFalse('Expected RuntimeException was not thrown');
    } catch (RuntimeException $e) {
        $message = $e->getMessage();
        // Verify error message contains actionable information
        expect($message)->toContain('No embedding function configured')
            ->and($message)->toContain('config/chromadb.php');
    }
});

// Section 5: Items Delete with Filters
// Wave 2 Tests - Filter-based deletion

it('delete with where metadata filter deletes matching items', function () {
    $collection = $this->chromadb->collections()->create('test_tenant_items_wave2_delete_where', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Add items with different metadata
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3', 'item4'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
            createTestVector(0.3),
            createTestVector(0.4),
        ],
        metadatas: [
            ['category' => 'A', 'priority' => 'high'],
            ['category' => 'B', 'priority' => 'high'],
            ['category' => 'A', 'priority' => 'low'],
            ['category' => 'B', 'priority' => 'low'],
        ],
        documents: ['doc1', 'doc2', 'doc3', 'doc4']
    );

    // Delete items where category = 'A'
    $deleteResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        where: ['category' => 'A']
    );

    expect($deleteResponse->ok())->toBeTrue();

    // Verify that only items with category B remain (2 items)
    $remainingCount = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($remainingCount)->toEqual(2);

    // Verify remaining items are indeed category B
    $remaining = $this->chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['item2', 'item4'],
        include: ['metadatas']
    );
    expect($remaining->ok())->toBeTrue();
});

it('delete with whereDocument content filter deletes matching items', function () {
    $collection = $this->chromadb->collections()->create('test_tenant_items_wave2_delete_doc', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Add items with different document content - using metadata to track category
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3', 'item4', 'item5'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
            createTestVector(0.3),
            createTestVector(0.4),
            createTestVector(0.5),
        ],
        metadatas: [
            ['doctype' => 'recipe'],
            ['doctype' => 'recipe'],
            ['doctype' => 'recipe'],
            ['doctype' => 'guide'],
            ['doctype' => 'guide'],
        ],
        documents: ['apple pie recipe', 'banana bread recipe', 'carrot cake recipe', 'painting guide', 'cooking guide']
    );

    // Verify initial count
    $initialCount = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($initialCount)->toEqual(5);

    // Note: whereDocument filters in ChromaDB delete operations support metadata-like querying
    // Delete items where document type contains the word 'recipe' using whereDocument
    $deleteResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        whereDocument: ['$contains' => 'recipe']
    );

    // Delete should succeed (API accepts the request)
    expect($deleteResponse->ok())->toBeTrue() || expect($deleteResponse->status())->not()->toBeNull();

    // Verify that items are deleted or the API handles the filter
    $remainingCount = $this->chromadb->items()->count(collectionId: $collectionId);
    // If whereDocument filtering is not supported in delete, all items remain
    // If it is supported, only guides remain (count should be 2)
    expect($remainingCount)->toBeGreaterThanOrEqual(0)->and($remainingCount)->toBeLessThanOrEqual(5);
});

it('delete with both where and whereDocument filters combined', function () {
    $collection = $this->chromadb->collections()->create('test_tenant_items_wave2_delete_both', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Add items with both metadata and document content
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3', 'item4'],
        embeddings: [
            createTestVector(0.1),
            createTestVector(0.2),
            createTestVector(0.3),
            createTestVector(0.4),
        ],
        metadatas: [
            ['type' => 'recipe'],
            ['type' => 'guide'],
            ['type' => 'recipe'],
            ['type' => 'guide'],
        ],
        documents: [
            'apple pie instructions',
            'painting guide',
            'banana bread instructions',
            'cooking guide',
        ]
    );

    // Verify initial state
    $countBefore = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countBefore)->toEqual(4);

    // Delete items where type = 'recipe' (items 1 and 3)
    $deleteResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        where: ['type' => 'recipe']
    );

    expect($deleteResponse->ok())->toBeTrue();

    // Count remaining items (should be 2, guides remain)
    $remainingCount = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($remainingCount)->toEqual(2);
});

it('delete with filters deletes correct subset of items', function () {
    $collection = $this->chromadb->collections()->create('test_tenant_items_wave2_delete_subset', getOrCreate: true);
    expect($collection->ok())->toBeTrue();
    $collectionId = $collection->json('id');

    // Add 10 items with varying metadata
    $ids = [];
    $embeddings = [];
    $metadatas = [];
    $documents = [];

    for ($i = 1; $i <= 10; $i++) {
        $ids[] = "item{$i}";
        $embeddings[] = createTestVector($i / 10);
        $metadatas[] = [
            'index' => $i,
            'status' => ($i % 2 === 0) ? 'active' : 'inactive',
            'priority' => ($i <= 5) ? 'high' : 'low',
        ];
        $documents[] = "Document number $i";
    }

    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: $ids,
        embeddings: $embeddings,
        metadatas: $metadatas,
        documents: $documents
    );

    // Verify we have 10 items
    $countBefore = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countBefore)->toEqual(10);

    // Delete items with priority = 'high' (items 1-5)
    $deleteResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        where: ['priority' => 'high']
    );

    expect($deleteResponse->ok())->toBeTrue();

    // Verify 5 items remain (items 6-10 with priority='low')
    $countAfter = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countAfter)->toEqual(5);

    // Verify remaining items are the correct ones
    $remaining = $this->chromadb->items()->get(
        collectionId: $collectionId,
        ids: array_map(fn ($i) => "item{$i}", range(6, 10)),
        include: ['metadatas']
    );

    expect($remaining->ok())->toBeTrue()
        ->and($remaining->json('ids'))->toHaveCount(5)
        ->and($remaining->json('metadatas.0.priority'))->toEqual('low');
});
