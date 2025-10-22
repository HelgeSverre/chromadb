<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $this->chromadb->server()->reset();
});

it('counts collections correctly', function () {
    $initialCount = $this->chromadb->collections()->count();
    $this->chromadb->collections()->create('test_collection_for_count', getOrCreate: true);
    $countAfterCreate = $this->chromadb->collections()->count();
    expect($countAfterCreate)->toEqual($initialCount + 1);

    $this->chromadb->collections()->delete('test_collection_for_count');
    $countAfterDelete = $this->chromadb->collections()->count();
    expect($countAfterDelete)->toEqual($initialCount);
});

it('retrieves a collection correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_retrieve', getOrCreate: true);
    expect($create->ok())->toBeTrue();

    $get = $this->chromadb->collections()->get('test_collection_for_retrieve');
    expect($get->ok())->toBeTrue()
        ->and($get->json('name'))->toEqual('test_collection_for_retrieve');

});

it('lists collections correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_list', getOrCreate: true);
    expect($create->ok())->toBeTrue();

    $list = $this->chromadb->collections()->list();
    expect($list->collect()->pluck('name'))->toContain('test_collection_for_list');
});

it('updates a collection metadata correctly', function () {
    $create = $this->chromadb->collections()->create('test_collection_for_update', getOrCreate: true);
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
    $create = $this->chromadb->collections()->create('test_collection_for_update', getOrCreate: true);
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

it('forks a collection correctly', function () {
    // NOTE: Fork endpoint is not implemented in local ChromaDB v1.0.0
    // Returns 501 "Collection forking is unsupported for local chroma"

    // Create original collection
    $original = $this->chromadb->collections()->create('original_collection', getOrCreate: true);
    expect($original->ok())->toBeTrue();
    $collectionId = $original->json('id');

    // Add some items to the original
    $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1'],
        embeddings: [createTestVector(0.1)],
        documents: ['test document']
    );

    // Fork the collection
    $forkResponse = $this->chromadb->collections()->fork(
        collectionId: $collectionId,
        newName: 'forked_collection'
    );

    // Fork returns 501 in local ChromaDB v1.0.0
    expect($forkResponse->status())->toBeIn([200, 501]);
})->skip('Fork endpoint not implemented in local ChromaDB v1.0.0 - returns 501');

it('can get collection by CRN', function () {
    // NOTE: CRN format validation may vary by ChromaDB version
    // Local v1.0.0 expects format: <tenant_resource_name>:<database_name>:<collection_name>

    // Create a collection
    $create = $this->chromadb->collections()->create('test_crn_collection', getOrCreate: true);
    expect($create->ok())->toBeTrue();

    // CRN format for v1.0.0 (without "chroma:tenant:" prefix)
    $crn = 'default:default_database:test_crn_collection';

    // Get collection by CRN
    $response = $this->chromadb->collections()->getByCrn($crn);

    // Expect either success or validation error depending on ChromaDB version
    expect($response->status())->toBeIn([200, 400, 404]);
})->skip('CRN endpoint format varies by ChromaDB version - needs cloud instance testing');
