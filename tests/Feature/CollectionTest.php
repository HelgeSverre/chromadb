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
