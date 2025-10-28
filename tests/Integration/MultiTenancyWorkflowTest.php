<?php

use HelgeSverre\Chromadb\Chromadb;

/**
 * Multi-Tenancy Workflow Integration Tests
 *
 * These tests verify that explicit tenant/database parameters in resource methods
 * correctly override the client's default settings, and that null parameters
 * fall back to client defaults.
 *
 * IMPORTANT: These tests MUST run sequentially due to tenant/database state dependencies.
 */
beforeEach(function () {
    // Create client with default tenant/database
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000',
        tenant: 'default_tenant',
        database: 'default_database'
    );

    // Reset server to clean state
    $this->chromadb->server()->reset();

    // Define test tenant and database names for isolation
    $this->testTenant = 'integration_test_tenant';
    $this->testDatabase = 'integration_test_db';
    $this->alternativeTenant = 'alternative_tenant';
    $this->alternativeDatabase = 'alternative_db';
});

afterEach(function () {
    // Clean up by resetting server
    $this->chromadb->server()->reset();
});

it('Collections methods accept and use explicit tenant parameter', function () {
    // Create a tenant for testing
    $createTenantResponse = $this->chromadb->tenant()->create($this->testTenant);
    expect($createTenantResponse->ok())->toBeTrue();

    // Create a database in the test tenant
    $createDbResponse = $this->chromadb->database()->create('default_database', $this->testTenant);
    expect($createDbResponse->ok())->toBeTrue();

    // Create a collection with explicit tenant parameter (should override default)
    $createResponse = $this->chromadb->collections()->create(
        name: 'test_collection_explicit_tenant',
        tenant: $this->testTenant,
        database: 'default_database'
    );

    expect($createResponse->ok())->toBeTrue();

    // Verify the collection was created in the explicit tenant
    // by listing collections with the explicit tenant parameter
    $listResponse = $this->chromadb->collections()->list(
        tenant: $this->testTenant,
        database: 'default_database'
    );

    expect($listResponse->ok())->toBeTrue();
    $collections = $listResponse->json();
    expect($collections)->toBeArray()
        ->and(count($collections))->toBeGreaterThan(0);

    // Find our collection
    $found = false;
    foreach ($collections as $collection) {
        if ($collection['name'] === 'test_collection_explicit_tenant') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // Verify listing with default tenant does NOT show this collection
    $defaultListResponse = $this->chromadb->collections()->list();
    $defaultCollections = $defaultListResponse->json();

    $foundInDefault = false;
    foreach ($defaultCollections as $collection) {
        if ($collection['name'] === 'test_collection_explicit_tenant') {
            $foundInDefault = true;
            break;
        }
    }
    expect($foundInDefault)->toBeFalse();
});

it('Collections methods accept and use explicit database parameter', function () {
    // Create a database for testing
    $createDbResponse = $this->chromadb->database()->create($this->testDatabase);
    expect($createDbResponse->ok())->toBeTrue();

    // Create a collection with explicit database parameter (should override default)
    $createResponse = $this->chromadb->collections()->create(
        name: 'test_collection_explicit_database',
        tenant: 'default_tenant',
        database: $this->testDatabase
    );

    expect($createResponse->ok())->toBeTrue();

    // Verify the collection was created in the explicit database
    $listResponse = $this->chromadb->collections()->list(
        tenant: 'default_tenant',
        database: $this->testDatabase
    );

    expect($listResponse->ok())->toBeTrue();
    $collections = $listResponse->json();
    expect($collections)->toBeArray()
        ->and(count($collections))->toBeGreaterThan(0);

    // Find our collection
    $found = false;
    foreach ($collections as $collection) {
        if ($collection['name'] === 'test_collection_explicit_database') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // Verify listing with default database does NOT show this collection
    $defaultListResponse = $this->chromadb->collections()->list();
    $defaultCollections = $defaultListResponse->json();

    $foundInDefault = false;
    foreach ($defaultCollections as $collection) {
        if ($collection['name'] === 'test_collection_explicit_database') {
            $foundInDefault = true;
            break;
        }
    }
    expect($foundInDefault)->toBeFalse();
});

it('Items methods override default tenant/database', function () {
    // Create test tenant and database
    $tenantResponse = $this->chromadb->tenant()->create($this->alternativeTenant);
    expect($tenantResponse->ok())->toBeTrue();

    $dbResponse = $this->chromadb->database()->create($this->alternativeDatabase, $this->alternativeTenant);
    expect($dbResponse->ok())->toBeTrue();

    // Create a collection in alternative tenant/database using withTenant/withDatabase
    $alternativeClient = $this->chromadb
        ->withTenant($this->alternativeTenant)
        ->withDatabase($this->alternativeDatabase);

    $createResponse = $alternativeClient->collections()->create(
        name: 'test_items_collection'
    );

    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Add items using alternative client (should use alternative tenant/database)
    $addResponse = $alternativeClient->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2'],
        embeddings: [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]],
        metadatas: [['type' => 'test1'], ['type' => 'test2']],
        documents: ['document 1', 'document 2']
    );

    expect($addResponse->successful())->toBeTrue();

    // Verify items were added by getting them back
    $getResponse = $alternativeClient->items()->get(
        collectionId: $collectionId,
        ids: ['item1', 'item2']
    );

    expect($getResponse->ok())->toBeTrue();
    $items = $getResponse->json();
    expect($items['ids'])->toEqual(['item1', 'item2'])
        ->and($items['documents'])->toEqual(['document 1', 'document 2']);
});

it('Database methods with explicit tenant parameter', function () {
    // Create a tenant for testing
    $createTenantResponse = $this->chromadb->tenant()->create($this->testTenant);
    expect($createTenantResponse->ok())->toBeTrue();

    // Create a database with explicit tenant parameter
    $createDbResponse = $this->chromadb->database()->create(
        name: 'test_db_explicit_tenant',
        tenant: $this->testTenant
    );

    expect($createDbResponse->ok())->toBeTrue();

    // Verify the database was created in the explicit tenant
    $getResponse = $this->chromadb->database()->get(
        database: 'test_db_explicit_tenant',
        tenant: $this->testTenant
    );

    expect($getResponse->ok())->toBeTrue();

    // List databases with explicit tenant
    $listResponse = $this->chromadb->database()->list(
        tenant: $this->testTenant
    );

    expect($listResponse->ok())->toBeTrue();
    $databases = $listResponse->json();
    expect($databases)->toBeArray();

    // Find our database
    $found = false;
    foreach ($databases as $db) {
        if ($db['name'] === 'test_db_explicit_tenant') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // Verify listing with default tenant does NOT show this database
    $defaultListResponse = $this->chromadb->database()->list();
    $defaultDatabases = $defaultListResponse->json();

    $foundInDefault = false;
    foreach ($defaultDatabases as $db) {
        if ($db['name'] === 'test_db_explicit_tenant') {
            $foundInDefault = true;
            break;
        }
    }
    expect($foundInDefault)->toBeFalse();
});

it('Tenant creation with explicit context', function () {
    // Create a tenant
    $createResponse = $this->chromadb->tenant()->create($this->testTenant);
    expect($createResponse->ok())->toBeTrue();

    // Get the tenant to verify it exists
    $getResponse = $this->chromadb->tenant()->get($this->testTenant);
    expect($getResponse->ok())->toBeTrue();

    $tenantData = $getResponse->json();
    expect($tenantData)->toHaveKey('name')
        ->and($tenantData['name'])->toEqual($this->testTenant);

    // Update the tenant with resource name
    $updateResponse = $this->chromadb->tenant()->update(
        tenantName: $this->testTenant,
        resourceName: 'test_resource'
    );

    expect($updateResponse->ok())->toBeTrue();
});

it('Server methods respect tenant context', function () {
    // Server methods like identity should respect the client's tenant context
    $identityResponse = $this->chromadb->server()->identity();

    expect($identityResponse->ok())->toBeTrue();

    $identity = $identityResponse->json();
    expect($identity)->toHaveKeys(['user_id', 'tenant', 'databases'])
        ->and($identity['tenant'])->toEqual('default_tenant')
        ->and($identity['databases'])->toBeArray();

    // Note: The server identity endpoint returns the server's default tenant,
    // not the client's configured tenant. This is expected ChromaDB behavior.
    // The client's tenant configuration is used in API requests, not identity.

    // Verify that creating resources with different tenants works correctly
    $this->chromadb->tenant()->create($this->alternativeTenant);

    // Create a new client with different tenant
    $alternativeClient = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000',
        tenant: $this->alternativeTenant,
        database: 'default_database'
    );

    // Create a database in the alternative tenant using the alternative client
    $createDbResponse = $alternativeClient->database()->create('test_db_alt');
    expect($createDbResponse->ok())->toBeTrue();

    // Verify the database exists in the alternative tenant
    $getDbResponse = $alternativeClient->database()->get('test_db_alt');
    expect($getDbResponse->ok())->toBeTrue();
});

it('explicit params override client defaults', function () {
    // Create a client with specific defaults
    $clientWithDefaults = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000',
        tenant: 'default_tenant',
        database: 'default_database'
    );

    // Create test tenant and database
    $clientWithDefaults->tenant()->create($this->testTenant);
    $clientWithDefaults->database()->create($this->testDatabase, $this->testTenant);

    // Create a collection with explicit tenant/database that overrides defaults
    $createResponse = $clientWithDefaults->collections()->create(
        name: 'test_override_collection',
        tenant: $this->testTenant,
        database: $this->testDatabase
    );

    expect($createResponse->ok())->toBeTrue();

    // Verify the collection exists in the explicit tenant/database, not defaults
    $listWithExplicit = $clientWithDefaults->collections()->list(
        tenant: $this->testTenant,
        database: $this->testDatabase
    );

    expect($listWithExplicit->ok())->toBeTrue();
    $explicitCollections = $listWithExplicit->json();

    $found = false;
    foreach ($explicitCollections as $collection) {
        if ($collection['name'] === 'test_override_collection') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // Verify the collection does NOT exist in the default tenant/database
    $listWithDefaults = $clientWithDefaults->collections()->list();
    $defaultCollections = $listWithDefaults->json();

    $foundInDefaults = false;
    foreach ($defaultCollections as $collection) {
        if ($collection['name'] === 'test_override_collection') {
            $foundInDefaults = true;
            break;
        }
    }
    expect($foundInDefaults)->toBeFalse();
});

it('null explicit params use client defaults', function () {
    // Create a client with specific defaults
    $clientWithDefaults = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000',
        tenant: 'default_tenant',
        database: 'default_database'
    );

    // Create a collection with null tenant/database (should use client defaults)
    $createResponse = $clientWithDefaults->collections()->create(
        name: 'test_null_params_collection',
        tenant: null,
        database: null
    );

    expect($createResponse->ok())->toBeTrue();

    // Verify the collection was created in the default tenant/database
    $listResponse = $clientWithDefaults->collections()->list(
        tenant: null,
        database: null
    );

    expect($listResponse->ok())->toBeTrue();
    $collections = $listResponse->json();

    $found = false;
    foreach ($collections as $collection) {
        if ($collection['name'] === 'test_null_params_collection') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // Also verify by explicitly specifying the defaults
    $explicitListResponse = $clientWithDefaults->collections()->list(
        tenant: 'default_tenant',
        database: 'default_database'
    );

    expect($explicitListResponse->ok())->toBeTrue();
    $explicitCollections = $explicitListResponse->json();

    $foundExplicit = false;
    foreach ($explicitCollections as $collection) {
        if ($collection['name'] === 'test_null_params_collection') {
            $foundExplicit = true;
            break;
        }
    }
    expect($foundExplicit)->toBeTrue();
});
