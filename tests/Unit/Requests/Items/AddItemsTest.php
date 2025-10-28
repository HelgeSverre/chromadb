<?php

use HelgeSverre\Chromadb\Requests\Items\AddItems;

describe('AddItems Request', function () {
    test('constructs correct URL endpoint', function () {
        $request = new AddItems(
            collectionId: 'test-collection-id',
            ids: ['id1'],
            embeddings: [[0.1, 0.2, 0.3]]
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toBe('/api/v2/tenants/default_tenant/databases/default_database/collections/test-collection-id/add');
    });

    test('includes tenant in URL when provided', function () {
        $request = new AddItems(
            collectionId: 'test-collection-id',
            ids: ['id1'],
            embeddings: [[0.1, 0.2, 0.3]],
            tenant: 'custom-tenant'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/custom-tenant/')
            ->not()->toContain('default_tenant');
    });

    test('includes database in URL when provided', function () {
        $request = new AddItems(
            collectionId: 'test-collection-id',
            ids: ['id1'],
            embeddings: [[0.1, 0.2, 0.3]],
            database: 'custom-database'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/custom-database/')
            ->not()->toContain('default_database');
    });

    test('serializes all parameters including embeddings, metadatas, and documents', function () {
        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.1, 0.2], [0.3, 0.4]],
            metadatas: [['key1' => 'value1'], ['key2' => 'value2']],
            documents: ['doc1', 'doc2']
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe(['id1', 'id2'])
            ->and($body)->toHaveKey('embeddings')
            ->and($body['embeddings'])->toBe([[0.1, 0.2], [0.3, 0.4]])
            ->and($body)->toHaveKey('metadatas')
            ->and($body['metadatas'])->toBe([['key1' => 'value1'], ['key2' => 'value2']])
            ->and($body)->toHaveKey('documents')
            ->and($body['documents'])->toBe(['doc1', 'doc2']);
    });

    test('excludes null parameters from body', function () {
        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1'],
            embeddings: [[0.1, 0.2]],
            metadatas: null,
            documents: null
        );

        $body = getRequestBody($request);

        expect($body)
            ->not()->toHaveKey('metadatas')
            ->not()->toHaveKey('documents')
            ->not()->toHaveKey('uris')
            ->toHaveKey('ids')
            ->toHaveKey('embeddings');
    });

    test('includes optional metadatas parameter when provided', function () {
        $metadatas = [
            ['tag' => 'important', 'priority' => 1],
            ['tag' => 'normal', 'priority' => 2],
        ];

        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.1, 0.2], [0.3, 0.4]],
            metadatas: $metadatas
        );

        $body = getRequestBody($request);

        expect($body['metadatas'])
            ->toBe($metadatas)
            ->toHaveCount(2);
    });

    test('includes optional documents parameter when provided', function () {
        $documents = ['This is document one', 'This is document two'];

        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.1, 0.2], [0.3, 0.4]],
            documents: $documents
        );

        $body = getRequestBody($request);

        expect($body['documents'])
            ->toBe($documents)
            ->toHaveCount(2);
    });

    test('uses correct HTTP method', function () {
        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1'],
            embeddings: [[0.1, 0.2]]
        );

        expect($request->getMethod()->value)->toBe('POST');
    });

    test('serializes URIs when provided', function () {
        $uris = ['http://example.com/doc1', 'http://example.com/doc2'];

        $request = new AddItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.1, 0.2], [0.3, 0.4]],
            uris: $uris
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('uris')
            ->and($body['uris'])->toBe($uris);
    });
});
