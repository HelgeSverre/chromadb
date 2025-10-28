<?php

use HelgeSverre\Chromadb\Requests\Items\DeleteItems;

describe('DeleteItems Request', function () {
    test('constructs correct URL endpoint with collection ID', function () {
        $request = new DeleteItems(
            collectionId: 'test-collection-id'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toBe('/api/v2/tenants/default_tenant/databases/default_database/collections/test-collection-id/delete');
    });

    test('includes custom tenant in URL', function () {
        $request = new DeleteItems(
            collectionId: 'collection-id',
            tenant: 'my-tenant'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/my-tenant/')
            ->not()->toContain('default_tenant');
    });

    test('includes custom database in URL', function () {
        $request = new DeleteItems(
            collectionId: 'collection-id',
            database: 'custom-db'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/custom-db/')
            ->not()->toContain('default_database');
    });

    test('serializes ids parameter in request body', function () {
        $ids = ['id1', 'id2', 'id3'];

        $request = new DeleteItems(
            collectionId: 'test-id',
            ids: $ids
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body['ids'])->toHaveCount(3);
    });

    test('serializes where metadata filter parameter', function () {
        $where = ['status' => 'archived', 'retention' => ['\$lt' => 30]];

        $request = new DeleteItems(
            collectionId: 'test-id',
            where: $where
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('where')
            ->and($body['where'])->toBe($where);
    });

    test('serializes whereDocument content filter parameter', function () {
        $whereDocument = ['\$contains' => 'deprecated'];

        $request = new DeleteItems(
            collectionId: 'test-id',
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe($whereDocument);
    });

    test('includes both ids and where filters when provided', function () {
        $ids = ['id1', 'id2'];
        $where = ['category' => 'temporary'];

        $request = new DeleteItems(
            collectionId: 'test-id',
            ids: $ids,
            where: $where
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body)->toHaveKey('where')
            ->and($body['where'])->toBe($where);
    });

    test('includes ids and whereDocument filters together', function () {
        $ids = ['id1'];
        $whereDocument = ['\$contains' => 'expired'];

        $request = new DeleteItems(
            collectionId: 'test-id',
            ids: $ids,
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body)->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe($whereDocument);
    });

    test('includes all filter types when provided', function () {
        $ids = ['id1', 'id2'];
        $where = ['status' => 'inactive'];
        $whereDocument = ['\$not_contains' => 'important'];

        $request = new DeleteItems(
            collectionId: 'test-id',
            ids: $ids,
            where: $where,
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body)->toHaveKey('where')
            ->and($body['where'])->toBe($where)
            ->and($body)->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe($whereDocument);
    });

    test('allows null ids parameter', function () {
        $request = new DeleteItems(
            collectionId: 'test-id',
            ids: null,
            where: ['archive' => true]
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBeNull()
            ->and($body)->toHaveKey('where')
            ->and($body['where'])->toBe(['archive' => true]);
    });

    test('uses correct HTTP method', function () {
        $request = new DeleteItems(
            collectionId: 'test-id'
        );

        expect($request->getMethod()->value)->toBe('POST');
    });

    test('handles complex where filters with operators', function () {
        $where = [
            '\$or' => [
                ['status' => 'deleted'],
                ['expiration' => ['\$lt' => time()]],
            ],
        ];

        $request = new DeleteItems(
            collectionId: 'test-id',
            where: $where
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('where')
            ->and($body['where'])->toBe($where);
    });

    test('handles complex whereDocument filters', function () {
        $whereDocument = [
            '\$or' => [
                ['\$contains' => 'deprecated'],
                ['\$contains' => 'obsolete'],
            ],
        ];

        $request = new DeleteItems(
            collectionId: 'test-id',
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe($whereDocument);
    });
});
