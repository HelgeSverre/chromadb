<?php

use HelgeSverre\Chromadb\Requests\Items\UpdateItems;

describe('UpdateItems Request', function () {
    test('constructs correct URL endpoint with collection ID', function () {
        $request = new UpdateItems(
            collectionId: 'test-collection-id',
            ids: ['id1']
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toBe('/api/v2/tenants/default_tenant/databases/default_database/collections/test-collection-id/update');
    });

    test('includes custom tenant in URL', function () {
        $request = new UpdateItems(
            collectionId: 'collection-123',
            ids: ['id1'],
            tenant: 'production'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/production/')
            ->not()->toContain('default_tenant');
    });

    test('includes custom database in URL', function () {
        $request = new UpdateItems(
            collectionId: 'collection-123',
            ids: ['id1'],
            database: 'analytics'
        );

        $endpoint = $request->resolveEndpoint();

        expect($endpoint)
            ->toContain('/analytics/')
            ->not()->toContain('default_database');
    });

    test('serializes ids parameter in request body', function () {
        $ids = ['id1', 'id2', 'id3'];

        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: $ids
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe($ids)
            ->and($body['ids'])->toHaveCount(3);
    });

    test('serializes all parameter types when provided', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.1, 0.2], [0.3, 0.4]],
            metadatas: [['tag' => 'updated'], ['tag' => 'current']],
            documents: ['updated doc 1', 'updated doc 2']
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->and($body['ids'])->toBe(['id1', 'id2'])
            ->and($body)->toHaveKey('embeddings')
            ->and($body['embeddings'])->toBe([[0.1, 0.2], [0.3, 0.4]])
            ->and($body)->toHaveKey('metadatas')
            ->and($body['metadatas'])->toBe([['tag' => 'updated'], ['tag' => 'current']])
            ->and($body)->toHaveKey('documents')
            ->and($body['documents'])->toBe(['updated doc 1', 'updated doc 2']);
    });

    test('supports partial updates with only embeddings', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            embeddings: [[0.5, 0.6], [0.7, 0.8]]
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->toHaveKey('embeddings')
            ->not()->toHaveKey('metadatas')
            ->not()->toHaveKey('documents');
    });

    test('supports partial updates with only metadatas', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1'],
            metadatas: [['status' => 'updated']]
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->toHaveKey('metadatas')
            ->not()->toHaveKey('embeddings')
            ->not()->toHaveKey('documents');
    });

    test('supports partial updates with only documents', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            documents: ['new content 1', 'new content 2']
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->toHaveKey('documents')
            ->not()->toHaveKey('embeddings')
            ->not()->toHaveKey('metadatas');
    });

    test('excludes null parameters from body', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1'],
            embeddings: null,
            metadatas: null,
            documents: null,
            uris: null
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('ids')
            ->not()->toHaveKey('embeddings')
            ->not()->toHaveKey('metadatas')
            ->not()->toHaveKey('documents')
            ->not()->toHaveKey('uris');
    });

    test('uses correct HTTP method', function () {
        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1']
        );

        expect($request->getMethod()->value)->toBe('POST');
    });

    test('serializes URIs when provided', function () {
        $uris = ['http://example.com/updated1', 'http://example.com/updated2'];

        $request = new UpdateItems(
            collectionId: 'test-id',
            ids: ['id1', 'id2'],
            uris: $uris
        );

        $body = getRequestBody($request);

        expect($body)
            ->toHaveKey('uris')
            ->and($body['uris'])->toBe($uris);
    });
});
