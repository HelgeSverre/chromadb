<?php

use HelgeSverre\Chromadb\Requests\Collections\ListCollections;

describe('ListCollections Request', function () {
    it('test pagination params: limit, offset', function () {
        $request = new ListCollections(
            limit: 50,
            offset: 100,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $query = callProtectedMethod($request, 'defaultQuery');

        expect($query)
            ->toHaveKey('limit', 50)
            ->toHaveKey('offset', 100);
    });

    it('test query string building', function () {
        $request = new ListCollections(
            limit: 25,
            offset: 10,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $query = callProtectedMethod($request, 'defaultQuery');

        expect($query)
            ->toBeArray()
            ->toHaveCount(2);
    });

    it('test default values when params not provided', function () {
        $request = new ListCollections(
            limit: null,
            offset: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $query = callProtectedMethod($request, 'defaultQuery');

        expect($query)
            ->toBeArray()
            ->toHaveCount(0);
    });
});
