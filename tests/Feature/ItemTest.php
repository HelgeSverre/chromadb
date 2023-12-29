<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    // Reset the server before each test
    $this->chromadb->server()->reset();
});

it('adds items with ids and metadatas  correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');

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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');

    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1'],
        metadatas: [['title' => 'metadata1']],
        documents: ['document1']
    );

    // Count items
    $itemCount = $this->chromadb->items()->count(collectionId: $collectionId);

    expect($itemCount)->toEqual(1);

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item2'],
        metadatas: [['title' => 'metadata2']],
        documents: ['document2']
    );

    // Count items again
    $itemCount = $this->chromadb->items()->count(collectionId: $collectionId);

    expect($itemCount)->toEqual(2);
});

it('retrieves items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: ['embedding1', 'embedding2'],
        metadatas: ['metadata1', 'metadata2'],
        documents: ['document1', 'document2']
    );

    // Retrieve items
    $getItemsResponse = $this->chromadb->items()->get(collectionId: $collectionId);
    expect($getItemsResponse->ok())->toBeTrue();
    expect($getItemsResponse->json())->toHaveCount(2);
});

it('updates items with ids, documents and metadatas correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();

    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item'],
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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();

    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item'],
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
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Upsert items
    $upsertItemsResponse = $this->chromadb->items()->upsert(
        collectionId: $collectionId,
        ids: ['item'],
        metadatas: [['title' => 'metadata']],
        documents: ['document']
    );

    expect($upsertItemsResponse->ok())->toBeTrue();
});

it('queries items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: ['embedding1', 'embedding2'],
        metadatas: ['metadata1', 'metadata2'],
        documents: ['document1', 'document2']
    );

    // Query items
    $queryItemsResponse = $this->chromadb->items()->query(
        collectionId: $collectionId,
        queryEmbeddings: ['query_embedding1'],
        where: ['field' => 'value'],
        whereDocument: ['field' => 'value'],
        include: ['embeddings', 'documents', 'metadatas'],
        nResults: 5
    );
    expect($queryItemsResponse->ok())->toBeTrue()
        ->and($queryItemsResponse->json())->toHaveCount(2);
});

it('deletes items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: [
            'item1',
            'item2',
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

    expect($deleteItemsResponse->ok())->toBeTrue()
        ->and($deleteItemsResponse->json())->toHaveCount(2)
        ->and($deleteItemsResponse->json())->toEqual([
            'item1',
            'item2',
        ]);

    // Count items after delete
    $countAfterDelete = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countAfterDelete)->toEqual(0);
});
