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

it('counts collections correctly', function () {
    $initialCount = $this->chromadb->collections()->count();
    $this->chromadb->collections()->create('test_collection_for_count');
    $countAfterCreate = $this->chromadb->collections()->count();
    expect($countAfterCreate)->toEqual($initialCount + 1);

    $this->chromadb->collections()->delete('test_collection_for_count');
    $countAfterDelete = $this->chromadb->collections()->count();
    expect($countAfterDelete)->toEqual($initialCount);
});

it('retrieves a collection correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_retrieve');
    expect($create->ok())->toBeTrue();

    $get = $this->chromadb->collections()->get('test_collection_for_retrieve');
    expect($get->ok())->toBeTrue()
        ->and($get->json('name'))->toEqual('test_collection_for_retrieve');

});

it('lists collections correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_list');
    expect($create->ok())->toBeTrue();

    $list = $this->chromadb->collections()->list();
    expect($list->collect()->pluck('name'))->toContain('test_collection_for_list');
});

it('updates a collection metadata correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_update');
    expect($create->ok())->toBeTrue();

    $update = $this->chromadb->collections()->update(
        collectionId: $create->json('id'),
        newMetadata: ['updated' => '100']
    );
    expect($update->ok())->toBeTrue();

    $get = $this->chromadb->collections()->get('test_collection_for_update');

    expect($get->ok())->toBeTrue()
        ->and($get->json('name'))->toEqual('test_collection_for_update')
        ->and($get->json('metadata.updated'))->toEqual(100);
});

it('updates a collection name correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_update');
    expect($create->ok())->toBeTrue();

    $update = $this->chromadb->collections()->update(
        collectionId: $create->json('id'),
        newName: 'test_collection_for_update_2'
    );
    expect($update->ok())->toBeTrue();

    $get = $this->chromadb->collections()->get('test_collection_for_update_2');

    expect($get->ok())->toBeTrue()
        ->and($get->json('name'))->toEqual('test_collection_for_update_2');

});
it('adds, counts, retrieves, updates, upserts, queries, and deletes items correctly', function () {
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
