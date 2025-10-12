<?php

use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;
use HelgeSverre\Chromadb\Facades\Chromadb;

beforeEach(function () {
    // Create a simple mock embedding function for testing
    $this->mockEmbeddingFunction = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            // Return simple embeddings for each text
            return array_map(fn ($text, $i) => createTestVector(0.1 + ($i * 0.01), 128), $texts, array_keys($texts));
        }
    };
});

test('addWithEmbeddings can add items with automatic embedding generation', function () {
    $client = Chromadb::client();

    // Reset ChromaDB
    $client->server()->reset();

    // Create a test collection
    $collection = $client->collections()->create(
        name: 'test_embeddings',
        metadata: ['test' => true]
    );

    $collectionId = $collection->json('id');

    // Add items with automatic embedding generation
    $response = $client->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['This is document 1', 'This is document 2'],
        embeddingFunction: $this->mockEmbeddingFunction,
        ids: ['doc1', 'doc2'],
        metadatas: [['source' => 'test1'], ['source' => 'test2']]
    );

    expect($response->successful())->toBeTrue();

    // Verify items were added
    $count = $client->items()->count($collectionId);
    expect($count)->toBe(2);

    // Get items and verify they have embeddings
    $items = $client->items()->get(
        collectionId: $collectionId,
        ids: ['doc1', 'doc2'],
        include: ['embeddings', 'documents', 'metadatas']
    );

    $data = $items->json();
    expect($data['ids'])->toHaveCount(2)
        ->and($data['embeddings'])->toBeArray()->toHaveCount(2)
        ->and($data['documents'])->toContain('This is document 1')
        ->and($data['metadatas'][0]['source'])->toBe('test1');
});

test('addWithEmbeddings auto-generates IDs when not provided', function () {
    $client = Chromadb::client();

    // Reset ChromaDB
    $client->server()->reset();

    // Create a test collection
    $collection = $client->collections()->create(
        name: 'test_auto_ids',
        metadata: ['test' => true]
    );

    $collectionId = $collection->json('id');

    // Add items without providing IDs
    $response = $client->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['Auto ID document 1', 'Auto ID document 2', 'Auto ID document 3'],
        embeddingFunction: $this->mockEmbeddingFunction
    );

    expect($response->successful())->toBeTrue();

    // Verify items were added
    $count = $client->items()->count($collectionId);
    expect($count)->toBe(3);

    // Get all items
    $items = $client->items()->get(
        collectionId: $collectionId,
        ids: [],
        include: ['documents'],
        limit: 10
    );

    $data = $items->json();
    expect($data['ids'])->toHaveCount(3)
        ->and($data['documents'])->toContain('Auto ID document 1');
});

test('queryWithText can query using text with automatic embedding generation', function () {
    $client = Chromadb::client();

    // Reset ChromaDB
    $client->server()->reset();

    // Create a test collection
    $collection = $client->collections()->create(
        name: 'test_query',
        metadata: ['test' => true]
    );

    $collectionId = $collection->json('id');

    // Add some items first
    $client->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: [
            'The quick brown fox jumps over the lazy dog',
            'A journey of a thousand miles begins with a single step',
            'To be or not to be, that is the question',
        ],
        embeddingFunction: $this->mockEmbeddingFunction,
        ids: ['doc1', 'doc2', 'doc3']
    );

    // Query using text
    $results = $client->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'fox and dog',
        embeddingFunction: $this->mockEmbeddingFunction,
        nResults: 2,
        include: ['documents', 'distances']
    );

    $data = $results->json();
    expect($data)->toHaveKey('ids')
        ->and($data)->toHaveKey('documents')
        ->and($data)->toHaveKey('distances')
        ->and($data['ids'][0])->toBeArray()->toHaveCount(2);
});

test('full workflow: add with embeddings and query with text', function () {
    $client = Chromadb::client();

    // Reset ChromaDB
    $client->server()->reset();

    // Create a test collection
    $collection = $client->collections()->create(
        name: 'test_full_workflow',
        metadata: ['test' => true]
    );

    $collectionId = $collection->json('id');

    // Step 1: Add multiple documents with embeddings
    $documents = [
        'Machine learning is a subset of artificial intelligence',
        'Deep learning uses neural networks with multiple layers',
        'Natural language processing helps computers understand human language',
        'Computer vision enables machines to interpret visual information',
    ];

    $client->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: $documents,
        embeddingFunction: $this->mockEmbeddingFunction,
        metadatas: array_map(fn ($i) => ['category' => 'ai', 'index' => $i], array_keys($documents))
    );

    // Step 2: Verify all items were added
    $count = $client->items()->count($collectionId);
    expect($count)->toBe(4);

    // Step 3: Query using text
    $results = $client->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'neural networks and deep learning',
        embeddingFunction: $this->mockEmbeddingFunction,
        nResults: 3,
        include: ['documents', 'metadatas', 'distances']
    );

    $data = $results->json();
    expect($data['ids'][0])->toBeArray()->toHaveCount(3)
        ->and($data['documents'][0])->toBeArray()->toHaveCount(3)
        ->and($data['metadatas'][0])->toBeArray()->toHaveCount(3)
        ->and($data['metadatas'][0][0])->toHaveKey('category')
        ->and($data['metadatas'][0][0]['category'])->toBe('ai');

    // Step 4: Query with metadata filter
    $filteredResults = $client->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'artificial intelligence',
        embeddingFunction: $this->mockEmbeddingFunction,
        nResults: 2,
        where: ['category' => 'ai'],
        include: ['documents']
    );

    expect($filteredResults->json())->toHaveKey('ids');
});
