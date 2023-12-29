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

it('adds items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: ['embedding1', 'embedding2'],
        metadatas: ['metadata1', 'metadata2'],
        documents: ['document1', 'document2']
    );
    expect($addItemsResponse->ok())->toBeTrue();
});

it('counts items correctly', function () {
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

    // Count items
    $countItemsResponse = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countItemsResponse->ok())->toBeTrue();
    expect($countItemsResponse->json())->toEqual(2);
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

it('updates items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: ['embedding1'],
        metadatas: ['metadata1'],
        documents: ['document1']
    );

    // Update items
    $updateItemsResponse = $this->chromadb->items()->update(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: ['updated_embedding1'],
        metadatas: ['updated_metadata1'],
        documents: ['updated_document1']
    );
    expect($updateItemsResponse->ok())->toBeTrue();
});

it('upserts items correctly', function () {
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Upsert items
    $upsertItemsResponse = $this->chromadb->items()->upsert(
        collectionId: $collectionId,
        ids: ['item3'],
        embeddings: ['embedding3'],
        metadatas: ['metadata3'],
        documents: ['document3']
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
    expect($queryItemsResponse->ok())->toBeTrue();
    expect($queryItemsResponse->json())->toHaveCount(2);
});

it('deletes items correctly', function () {
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

    // Delete items
    $deleteItemsResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        ids: ['item1', 'item2']
    );
    expect($deleteItemsResponse->ok())->toBeTrue();

    // Count items after delete
    $countAfterDeleteResponse = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countAfterDeleteResponse->ok())->toBeTrue();
    expect($countAfterDeleteResponse->json())->toEqual(0);
});
    $collectionCreate = $this->chromadb->collections()->create('test_collection_for_items');
    expect($collectionCreate->ok())->toBeTrue();
    $collectionId = $collectionCreate->json('id');

    // Add items
    $addItemsResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: ['embedding1', 'embedding2'],
        metadatas: ['metadata1', 'metadata2'],
        documents: ['document1', 'document2']
    );
    expect($addItemsResponse->ok())->toBeTrue();

    // Count items
    $countItemsResponse = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countItemsResponse->ok())->toBeTrue();
    expect($countItemsResponse->json())->toEqual(2);

    // Retrieve items
    $getItemsResponse = $this->chromadb->items()->get(collectionId: $collectionId);
    expect($getItemsResponse->ok())->toBeTrue();
    expect($getItemsResponse->json())->toHaveCount(2);

    // Update items
    $updateItemsResponse = $this->chromadb->items()->update(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: ['updated_embedding1'],
        metadatas: ['updated_metadata1'],
        documents: ['updated_document1']
    );
    expect($updateItemsResponse->ok())->toBeTrue();

    // Upsert items
    $upsertItemsResponse = $this->chromadb->items()->upsert(
        collectionId: $collectionId,
        ids: ['item3'],
        embeddings: ['embedding3'],
        metadatas: ['metadata3'],
        documents: ['document3']
    );
    expect($upsertItemsResponse->ok())->toBeTrue();

    // Query items
    $queryItemsResponse = $this->chromadb->items()->query(
        collectionId: $collectionId,
        queryEmbeddings: ['query_embedding1'],
        where: ['field' => 'value'],
        whereDocument: ['field' => 'value'],
        include: ['embeddings', 'documents', 'metadatas'],
        nResults: 5
    );
    expect($queryItemsResponse->ok())->toBeTrue();
    expect($queryItemsResponse->json())->toHaveCount(2);

    // Delete items
    $deleteItemsResponse = $this->chromadb->items()->delete(
        collectionId: $collectionId,
        ids: ['item1', 'item2']
    );
    expect($deleteItemsResponse->ok())->toBeTrue();

    // Count items after delete
    $countAfterDeleteResponse = $this->chromadb->items()->count(collectionId: $collectionId);
    expect($countAfterDeleteResponse->ok())->toBeTrue();
    expect($countAfterDeleteResponse->json())->toEqual(1);
});
