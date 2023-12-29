<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );
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
    $this->chromadb->collections()->create('test_collection_for_retrieve');
    $response = $this->chromadb->collections()->get('test_collection_for_retrieve');
    expect($response->json('name'))->toEqual('test_collection_for_retrieve');
    $this->chromadb->collections()->delete('test_collection_for_retrieve');
});

it('lists collections correctly', function () {
    $this->chromadb->collections()->create('test_collection_for_list');
    $response = $this->chromadb->collections()->list();
    expect($response->collect()->pluck('name'))->toContain('test_collection_for_list');
    $this->chromadb->collections()->delete('test_collection_for_list');
});

it('updates a collection correctly', function () {
    $response = $this->chromadb->collections()->create('test_collection_for_update', getOrCreate: true);
    $collectionId = $response->json('id');
    $updateResponse = $this->chromadb->collections()->update(
        collectionId: $collectionId,
        newMetadata: ['updated' => '100']
    );
    expect($updateResponse->status())->toEqual(200);
    $getResponse = $this->chromadb->collections()->get('test_collection_for_update');
    expect($getResponse->json('metadata.updated'))->toEqual(100);
    $this->chromadb->collections()->delete('test_collection_for_update');
});
