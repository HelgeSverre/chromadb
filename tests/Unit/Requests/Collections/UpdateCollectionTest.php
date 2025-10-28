<?php

use HelgeSverre\Chromadb\Requests\Collections\UpdateCollection;

describe('UpdateCollection Request', function () {
    it('test URL construction with collection ID', function () {
        $collectionId = '550e8400-e29b-41d4-a716-446655440000';
        $request = new UpdateCollection(
            collectionId: $collectionId,
            newName: 'updated-name',
            newMetadata: null,
            newConfiguration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        expect($request->resolveEndpoint())
            ->toContain($collectionId);
    });

    it('test name update payload', function () {
        $collectionId = '550e8400-e29b-41d4-a716-446655440000';
        $request = new UpdateCollection(
            collectionId: $collectionId,
            newName: 'new-collection-name',
            newMetadata: null,
            newConfiguration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $body = callProtectedMethod($request, 'defaultBody');

        expect($body)
            ->toHaveKey('new_name', 'new-collection-name');
    });

    it('test metadata update payload', function () {
        $collectionId = '550e8400-e29b-41d4-a716-446655440000';
        $newMetadata = [
            'description' => 'Updated description',
            'updated_at' => '2025-10-28',
        ];

        $request = new UpdateCollection(
            collectionId: $collectionId,
            newName: null,
            newMetadata: $newMetadata,
            newConfiguration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $body = callProtectedMethod($request, 'defaultBody');

        expect($body)
            ->toHaveKey('new_metadata', $newMetadata);
    });

    it('test empty update when no parameters provided', function () {
        $collectionId = '550e8400-e29b-41d4-a716-446655440000';
        $request = new UpdateCollection(
            collectionId: $collectionId,
            newName: null,
            newMetadata: null,
            newConfiguration: null,
            tenant: 'test-tenant',
            database: 'test-database'
        );

        $body = callProtectedMethod($request, 'defaultBody');

        expect($body)
            ->toBeArray()
            ->toHaveCount(0);
    });
});
