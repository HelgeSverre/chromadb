<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: "test-token-chroma-local-dev",
        host: "http://localhost",
        port: "8000"
    );
});

it('creates a collection and confirms if it exists in the list', function () {

    $this->chromadb->collections()->delete('test_collection');

    $response = $this->chromadb->collections()->create(
        collectionName: 'test_collection',
    );

    expect($response->json('code'))->toEqual(200);

    sleep(1);

    $response = $this->milvus->collections()->list();

    expect($response->collect('data'))->toContain('test_collection');

    $response = $this->chromadb->collections()->delete(collectionName: 'test_collection');
    expect($response->json('code'))->toEqual(200);

    sleep(1);

    $response = $this->milvus->collections()->list();

    expect($response->collect('data'))->not->toContain('test_collection');

});

it('can insert stuff into collections', function () {

    $this->milvus->collections()->create(
        collectionName: 'add_stuff_into_collections',
        dimension: 128,
    );

    $insert = $this->milvus->vector()->insert(
        collectionName: 'add_stuff_into_collections',
        data: [
            ['vector' => createTestVector(0.1)],
            ['vector' => createTestVector(0.2)],
            ['vector' => createTestVector(0.3)],
        ],
    );

    $insertedIds = $insert->collect('data.insertIds')->join(',');

    expect($insert->collect('data.insertIds')->count())->toEqual(3);

    sleep(1);

    $query = $this->milvus->vector()->query(
        collectionName: 'add_stuff_into_collections',
        filter: "id in [$insertedIds]",
    );

    expect($query->collect('data')->count())->toEqual(3);

});

it('counts collections correctly', function () {
    $initialCountResponse = $this->chromadb->collections()->count();
    $initialCount = $initialCountResponse->json('data');

    $this->chromadb->collections()->create('test_collection_for_count');
    sleep(1);

    $countAfterCreateResponse = $this->chromadb->collections()->count();
    $countAfterCreate = $countAfterCreateResponse->json('data');

    expect($countAfterCreate)->toEqual($initialCount + 1);

    $this->chromadb->collections()->delete('test_collection_for_count');
    sleep(1);

    $countAfterDeleteResponse = $this->chromadb->collections()->count();
    $countAfterDelete = $countAfterDeleteResponse->json('data');

    expect($countAfterDelete)->toEqual($initialCount);
});

it('retrieves a collection correctly', function () {
    $this->chromadb->collections()->create('test_collection_for_retrieve');
    sleep(1);

    $response = $this->chromadb->collections()->get('test_collection_for_retrieve');

    expect($response->json('data.name'))->toEqual('test_collection_for_retrieve');

    $this->chromadb->collections()->delete('test_collection_for_retrieve');
});

it('lists collections correctly', function () {
    $this->chromadb->collections()->create('test_collection_for_list');
    sleep(1);

    $response = $this->chromadb->collections()->list();

    expect($response->collect('data'))->toContain('test_collection_for_list');

    $this->chromadb->collections()->delete('test_collection_for_list');
});

it('updates a collection correctly', function () {
    $this->chromadb->collections()->create('test_collection_for_update');
    sleep(1);

    $updateResponse = $this->chromadb->collections()->update(
        'test_collection_for_update',
        metadata: ['updated' => true]
    );

    expect($updateResponse->json('code'))->toEqual(200);

    $getResponse = $this->chromadb->collections()->get('test_collection_for_update');
    expect($getResponse->json('data.metadata.updated'))->toBeTrue();

    $this->chromadb->collections()->delete('test_collection_for_update');
});
