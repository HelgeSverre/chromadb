<?php

use HelgeSverre\Chromadb\Requests\Collections\GetAttachedFunction;

describe('GetAttachedFunction Request', function () {
    it('resolves endpoint with function name', function () {
        $request = new GetAttachedFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats',
            tenant: 'custom-tenant',
            database: 'custom-database'
        );

        expect($request->resolveEndpoint())
            ->toBe('/api/v2/tenants/custom-tenant/databases/custom-database/collections/test-collection-id/functions/my-stats');
    });

    it('uses default tenant and database when not provided', function () {
        $request = new GetAttachedFunction(
            collectionId: 'test-collection-id',
            functionName: 'my-stats'
        );

        expect($request->resolveEndpoint())
            ->toContain('default_tenant')
            ->toContain('default_database')
            ->toContain('functions/my-stats');
    });

    it('handles function names with special characters', function () {
        $request = new GetAttachedFunction(
            collectionId: 'test-collection-id',
            functionName: 'stats-v2'
        );

        expect($request->resolveEndpoint())
            ->toContain('functions/stats-v2');
    });
});
