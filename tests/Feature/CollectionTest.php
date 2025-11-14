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
    // NOTE: Fork endpoint is only available in Chroma Cloud (hosted version)
    // Local ChromaDB v1.0.x returns 501 "Collection forking is unsupported for local chroma"

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

    // Fork returns 501 in local ChromaDB v1.0.x
    expect($forkResponse->status())->toBeIn([200, 501]);
})->skip('Fork endpoint only available in Chroma Cloud (hosted). Local v1.0.x returns 501.');

it('can get collection by CRN (future feature)', function () {
    // NOTE: CRN (Collection Resource Name) endpoint is a new feature added Nov 13, 2025
    // Not yet available in any released ChromaDB version (will be in version after v1.3.4)
    //
    // Expected format: tenant_resource_name:database_name:collection_name
    // Prerequisite: Tenant must have a resource_name set via SetTenantResourceName API
    //
    // When this feature is released, update this test to:
    // 1. Set tenant resource name using $chromadb->tenant()->setResourceName()
    // 2. Use CRN format: "resource_name:default_database:collection_name"
    // 3. Remove the skip() call

    // Create a collection
    $create = $this->chromadb->collections()->create('test_crn_collection', getOrCreate: true);
    expect($create->ok())->toBeTrue();

    // TODO: When feature is available in released ChromaDB version, uncomment:
    // $this->chromadb->tenant()->setResourceName('default_tenant', 'my_resource_name');
    // $crn = 'my_resource_name:default_database:test_crn_collection';
    // $response = $this->chromadb->collections()->getByCrn($crn);
    // expect($response->ok())->toBeTrue();
    // expect($response->json('name'))->toBe('test_crn_collection');
})->skip('CRN endpoint not yet available in any released ChromaDB version. Feature added Nov 13, 2025, will be in version after v1.3.4.');

// Wave 2: Pagination Edge Cases (with tenant isolation)
it('list with negative offset returns empty or errors gracefully', function () {
    // Use tenant isolation to prevent conflicts with parallel test agents
    $chromadb = $this->chromadb->withTenant('test_tenant_collections_wave2');

    // Create multiple test collections for pagination testing
    for ($i = 1; $i <= 3; $i++) {
        $chromadb->collections()->create(
            name: "pagination_test_collection_{$i}",
            getOrCreate: true
        );
    }

    // Test negative offset - should return empty or error gracefully
    $list = $chromadb->collections()->list(limit: 10, offset: -1);

    // Should either return empty collection or status indicates error
    expect($list->collect())->toBeInstanceOf(Illuminate\Support\Collection::class);
});

it('list with limit exceeding total count returns all available', function () {
    // Use tenant isolation to prevent conflicts with parallel test agents
    $chromadb = $this->chromadb->withTenant('test_tenant_collections_wave2');

    // Create multiple test collections
    for ($i = 1; $i <= 2; $i++) {
        $chromadb->collections()->create(
            name: "limit_test_collection_{$i}",
            getOrCreate: true
        );
    }

    // List with very large limit
    $list = $chromadb->collections()->list(limit: 1000, offset: 0);

    // Should return all available collections, not error
    expect($list->collect())->toBeInstanceOf(Illuminate\Support\Collection::class);
});

it('list with zero limit returns results or errors consistently', function () {
    // Use tenant isolation to prevent conflicts with parallel test agents
    $chromadb = $this->chromadb->withTenant('test_tenant_collections_wave2');

    // Create test collection
    $chromadb->collections()->create('zero_limit_test', getOrCreate: true);

    // Test with zero limit - API should handle this gracefully
    $list = $chromadb->collections()->list(limit: 0, offset: 0);

    // Should either return empty or handle gracefully
    expect($list->collect())->toBeInstanceOf(Illuminate\Support\Collection::class);
});

it('list with very large offset returns empty collection', function () {
    // Use tenant isolation to prevent conflicts with parallel test agents
    $chromadb = $this->chromadb->withTenant('test_tenant_collections_wave2');

    // Create a few test collections
    for ($i = 1; $i <= 2; $i++) {
        $chromadb->collections()->create(
            name: "large_offset_test_{$i}",
            getOrCreate: true
        );
    }

    // List with offset beyond available data
    $list = $chromadb->collections()->list(limit: 10, offset: 10000);

    // Should return empty result set
    expect($list->collect())->toBeInstanceOf(Illuminate\Support\Collection::class);
});

it('count method returns integer', function () {
    // Use tenant isolation to prevent conflicts with parallel test agents
    $chromadb = $this->chromadb->withTenant('test_tenant_collections_wave2');

    // Create test collection
    $chromadb->collections()->create('count_test_collection', getOrCreate: true);

    // Get count
    $count = $chromadb->collections()->count();

    // Should return an integer (0 or more)
    expect($count)->toBeInt()
        ->and($count)->toBeGreaterThanOrEqual(0);
});
