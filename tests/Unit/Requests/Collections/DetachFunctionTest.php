<?php

use HelgeSverre\Chromadb\Requests\Collections\DetachFunction;

describe('DetachFunction Request', function () {
    it('resolves endpoint with function name', function () {
        $request = new DetachFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats',
            deleteOutput: false,
            tenant: 'custom-tenant',
            database: 'custom-database'
        );

        expect($request->resolveEndpoint())
            ->toBe('/api/v2/tenants/custom-tenant/databases/custom-database/collections/test-collection-id/attached_functions/my-stats/detach');
    });

    it('uses default tenant and database when not provided', function () {
        $request = new DetachFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats'
        );

        expect($request->resolveEndpoint())
            ->toContain('default_tenant')
            ->toContain('default_database')
            ->toContain('attached_functions/my-stats/detach');
    });

    it('includes delete_output in body', function () {
        $request = new DetachFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats',
            deleteOutput: true
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('delete_output', true);
    });

    it('defaults delete_output to false', function () {
        $request = new DetachFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats'
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('delete_output', false);
    });
});
