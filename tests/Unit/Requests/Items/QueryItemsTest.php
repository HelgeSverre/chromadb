<?php

use HelgeSverre\Chromadb\Requests\Items\QueryItems;

describe('QueryItems Request', function () {
    test('constructs correct URL endpoint with collection ID', function () {
        $request = new QueryItems(
            collectionId: 'test-collection-id',
            queryEmbeddings: [[0.1, 0.2, 0.3]]
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toBe('/api/v2/tenants/default_tenant/databases/default_database/collections/test-collection-id/query');
    });

    test('uses custom tenant in URL when provided', function () {
        $request = new QueryItems(
            collectionId: 'test-collection-id',
            queryEmbeddings: [[0.1, 0.2, 0.3]],
            tenant: 'my-tenant'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/my-tenant/')
            ->not()->toContain('default_tenant');
    });

    test('uses custom database in URL when provided', function () {
        $request = new QueryItems(
            collectionId: 'test-collection-id',
            queryEmbeddings: [[0.1, 0.2, 0.3]],
            database: 'my-database'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/my-database/')
            ->not()->toContain('default_database');
    });

    test('serializes queryEmbeddings parameter in body', function () {
        $embeddings = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]];

        $request = new QueryItems(
            collectionId: 'test-id',
            queryEmbeddings: $embeddings
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('query_embeddings')
            ->and($body['query_embeddings'])->toBe($embeddings);
    });

    test('supports queryTexts parameter as alternative to embeddings', function () {
        $texts = ['search text 1', 'search text 2'];

        $request = new QueryItems(
            collectionId: 'test-id',
            queryTexts: $texts
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('query_texts')
            ->and($body['query_texts'])->toBe($texts);
    });

    test('includes both queryEmbeddings and queryTexts when both provided', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            queryEmbeddings: [[0.1, 0.2, 0.3]],
            queryTexts: ['search text']
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('query_embeddings')
            ->and($body['query_embeddings'])->toBe([[0.1, 0.2, 0.3]])
            ->and($body)->toHaveKey('query_texts')
            ->and($body['query_texts'])->toBe(['search text']);
    });

    test('serializes include parameter array', function () {
        $include = ['embeddings', 'metadatas', 'documents'];

        $request = new QueryItems(
            collectionId: 'test-id',
            include: $include
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('include')
            ->and($body['include'])->toBe($include)
            ->and($body['include'])->toHaveCount(3);
    });

    test('serializes nResults parameter', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            nResults: 10
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('n_results')
            ->and($body['n_results'])->toBe(10);
    });

    test('serializes where filter parameter', function () {
        $where = ['status' => 'active', 'score' => ['\$gt' => 0.5]];

        $request = new QueryItems(
            collectionId: 'test-id',
            where: $where
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('where')
            ->and($body['where'])->toBe($where);
    });

    test('serializes whereDocument filter parameter', function () {
        $whereDocument = ['\$contains' => 'important'];

        $request = new QueryItems(
            collectionId: 'test-id',
            whereDocument: $whereDocument
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('where_document')
            ->and($body['where_document'])->toBe($whereDocument);
    });

    test('includes pagination parameters in query string', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            limit: 20,
            offset: 10
        );

        $queryString = getRequestQuery($request);

        expect($queryString)
            ->toHaveKey('limit')
            ->and($queryString['limit'])->toBe(20)
            ->and($queryString)->toHaveKey('offset')
            ->and($queryString['offset'])->toBe(10);
    });

    test('excludes null pagination parameters from query string', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            limit: null,
            offset: null
        );

        $queryString = getRequestQuery($request);

        expect($queryString)
            ->not()->toHaveKey('limit')
            ->not()->toHaveKey('offset');
    });

    test('includes only non-null pagination parameters', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            limit: 15
        );

        $queryString = getRequestQuery($request);

        expect($queryString)
            ->toHaveKey('limit')
            ->and($queryString['limit'])->toBe(15)
            ->and($queryString)->not()->toHaveKey('offset');
    });

    test('uses correct HTTP method', function () {
        $request = new QueryItems(
            collectionId: 'test-id'
        );

        expect($request->getMethod()->value)->toBe('POST');
    });

    test('builds complete query with all parameters', function () {
        $request = new QueryItems(
            collectionId: 'test-id',
            queryEmbeddings: [[0.1, 0.2, 0.3]],
            queryTexts: ['search term'],
            where: ['category' => 'science'],
            whereDocument: ['\$contains' => 'quantum'],
            include: ['embeddings', 'metadatas', 'documents'],
            nResults: 5,
            limit: 100,
            offset: 20
        );

        $body = getRequestBody($request);
        $queryString = getRequestQuery($request);

        expect($body)
            ->toHaveKey('query_embeddings')
            ->and($body)->toHaveKey('query_texts')
            ->and($body)->toHaveKey('where')
            ->and($body)->toHaveKey('where_document')
            ->and($body)->toHaveKey('include')
            ->and($body)->toHaveKey('n_results');

        expect($queryString)
            ->toHaveKey('limit')
            ->and($queryString['limit'])->toBe(100)
            ->and($queryString)->toHaveKey('offset')
            ->and($queryString['offset'])->toBe(20);
    });
});
