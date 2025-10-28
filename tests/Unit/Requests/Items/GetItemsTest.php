<?php

use HelgeSverre\Chromadb\Requests\Items\GetItems;

describe('GetItems Request', function () {
    test('constructs correct URL endpoint with collection ID', function () {
        $request = new GetItems(
            collectionId: 'test-collection-id',
            ids: 'id1'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toBe('/api/v2/tenants/default_tenant/databases/default_database/collections/test-collection-id/get');
    });

    test('includes custom tenant in URL', function () {
        $request = new GetItems(
            collectionId: 'collection-id',
            ids: ['id1'],
            tenant: 'staging'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/staging/')
            ->not()->toContain('default_tenant');
    });

    test('includes custom database in URL', function () {
        $request = new GetItems(
            collectionId: 'collection-id',
            ids: 'id1',
            database: 'reporting'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/reporting/')
            ->not()->toContain('default_database');
    });

    test('serializes single id string parameter', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: 'single-id'
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe('single-id');
    });

    test('serializes array of ids', function () {
        $ids = ['id1', 'id2', 'id3'];

        $request = new GetItems(
            collectionId: 'test-id',
            ids: $ids
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body['ids'])->toHaveCount(3);
    });

    test('serializes include parameter array', function () {
        $include = ['embeddings', 'metadatas', 'documents'];

        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1'],
            include: $include
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('include')
            ->and($body['include'])->toBe($include)
            ->and($body['include'])->toHaveCount(3);
    });

    test('excludes null include parameter', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1'],
            include: null
        );

        $body = getRequestBody($request);

        expect($body)
            ->not()->toHaveKey('include');
    });

    test('includes limit parameter when provided', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            limit: 50
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('limit')
            ->and($body['limit'])->toBe(50);
    });

    test('includes offset parameter when provided', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1'],
            offset: 10
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('offset')
            ->and($body['offset'])->toBe(10);
    });

    test('includes where filter parameter', function () {
        $where = ['status' => 'active', 'type' => 'article'];

        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1'],
            where: $where
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('where')
            ->and($body['where'])->toBe($where);
    });

    test('includes whereDocument filter parameter', function () {
        $whereDocument = ['\$contains' => 'keyword'];

        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1'],
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe($whereDocument);
    });

    test('serializes complete request with all parameters', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2', 'id3'],
            include: ['embeddings', 'metadatas'],
            limit: 25,
            offset: 5,
            where: ['published' => true],
            whereDocument: ['\$contains' => 'featured']
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe(['id1', 'id2', 'id3'])
            ->and($body)->toHaveKey('include')
            ->and($body['include'])->toBe(['embeddings', 'metadatas'])
            ->and($body)->toHaveKey('limit')
            ->and($body['limit'])->toBe(25)
            ->and($body)->toHaveKey('offset')
            ->and($body['offset'])->toBe(5)
            ->and($body)->toHaveKey('where')
            ->and($body['where'])->toBe(['published' => true])
            ->and($body)->toHaveKey('whereDocument')
            ->and($body['whereDocument'])->toBe(['\$contains' => 'featured']);
    });

    test('uses correct HTTP method', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: 'id1'
        );

        expect($request->getMethod()->value)->toBe('POST');
    });

    test('excludes null optional parameters', function () {
        $request = new GetItems(
            collectionId: 'test-id',
            ids: 'id1',
            include: null,
            limit: null,
            offset: null,
            where: null,
            whereDocument: null
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->not()->toHaveKey('include')
            ->not()->toHaveKey('limit')
            ->not()->toHaveKey('offset')
            ->not()->toHaveKey('where')
            ->not()->toHaveKey('whereDocument');
    });
});
