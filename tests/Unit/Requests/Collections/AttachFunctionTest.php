<?php

use HelgeSverre\Chromadb\Requests\Collections\AttachFunction;

describe('AttachFunction Request', function () {
    it('resolves endpoint with tenant and database', function () {
        $request = new AttachFunction(
            collectionId: 'test-collection-id',
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output',
            params: null,
            tenant: 'custom-tenant',
            database: 'custom-database'
        );

        expect($request->resolveEndpoint())
            ->toBe('/api/v2/tenants/custom-tenant/databases/custom-database/collections/test-collection-id/functions/attach');
    });

    it('uses default tenant and database when not provided', function () {
        $request = new AttachFunction(
            collectionId: 'test-collection-id',
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output'
        );

        expect($request->resolveEndpoint())
            ->toContain('default_tenant')
            ->toContain('default_database');
    });

    it('includes required fields in body', function () {
        $request = new AttachFunction(
            collectionId: 'test-collection-id',
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output'
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('name', 'my-stats')
            ->toHaveKey('function_id', 'statistics')
            ->toHaveKey('output_collection', 'stats-output');
    });

    it('includes params when provided', function () {
        $params = ['key' => 'value', 'enabled' => true];

        $request = new AttachFunction(
            collectionId: 'test-collection-id',
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output',
            params: $params
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('params', $params);
    });

    it('excludes params when null', function () {
        $request = new AttachFunction(
            collectionId: 'test-collection-id',
            name: 'my-stats',
            functionId: 'statistics',
            outputCollection: 'stats-output',
            params: null
        );

        $body = getRequestBody($request);

        expect($body)->not()->toHaveKey('params');
    });
});
