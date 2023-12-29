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
