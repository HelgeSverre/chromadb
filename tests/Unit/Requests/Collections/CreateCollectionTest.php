<?php

use HelgeSverre\Chromadb\Requests\Collections\CreateCollection;

describe('CreateCollection Request', function () {
    it('test URL: /api/v2/collections', function () {
        $request = new CreateCollection(
            name: 'test-collection',
            getOrCreate: false,
            metadata: null,
            configuration: null,
            tenant: null,
            database: null
        );

        expect($request->resolveEndpoint())
            ->toContain('/collections');
    });

    it('test name parameter is required', function () {
        $request = new CreateCollection(
            name: 'my-collection',
            getOrCreate: false,
            metadata: null,
            configuration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $body = callProtectedMethod($request, 'defaultBody');

        expect($body)
            ->toHaveKey('name', 'my-collection');
    });

    it('test optional metadata parameter serialization', function () {
        $metadata = [
            'description' => 'Test collection',
            'author' => 'test-user',
        ];

        $request = new CreateCollection(
            name: 'test-collection',
            getOrCreate: false,
            metadata: $metadata,
            configuration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $body = callProtectedMethod($request, 'defaultBody');

        expect($body)
            ->toHaveKey('metadata', $metadata);
    });

    it('test tenant/database context headers are included', function () {
        $request = new CreateCollection(
            name: 'test-collection',
            getOrCreate: false,
            metadata: null,
            configuration: null,
            tenant: 'custom-tenant',
            database: 'custom-database'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('custom-tenant')
            ->toContain('custom-database');
    });
});
