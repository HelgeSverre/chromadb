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

it('updates tenant resource_name successfully', function () {
    // Create a tenant first
    $createResponse = $this->chromadb->tenant()->create('test_tenant');
    expect($createResponse->ok())->toBeTrue();

    // Update the tenant
    $updateResponse = $this->chromadb->tenant()->update(
        tenantName: 'test_tenant',
        resourceName: 'my_resource'
    );

    expect($updateResponse->ok())->toBeTrue();
});

it('handles updating non-existent tenant', function () {
    $updateResponse = $this->chromadb->tenant()->update(
        tenantName: 'non_existent_tenant',
        resourceName: 'my_resource'
    );

    // ChromaDB creates the tenant if it doesn't exist (returns 200)
    // This is expected behavior
    expect($updateResponse->status())->toBeIn([200, 404]);
});

it('gets user identity correctly', function () {
    $response = $this->chromadb->server()->identity();

    expect($response->ok())->toBeTrue()
        ->and($response->json())->toHaveKeys(['user_id', 'tenant', 'databases'])
        ->and($response->json('tenant'))->toEqual('default_tenant')
        ->and($response->json('databases'))->toBeArray();
});
