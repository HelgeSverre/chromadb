<?php

use HelgeSverre\Chromadb\Chromadb;
use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;

/**
 * README Examples Integration Tests
 *
 * These tests verify that the code examples shown in the README.md file
 * work correctly in practice. This ensures documentation accuracy.
 *
 * Tests focus on:
 * - addWithEmbeddings() method with various parameter combinations
 * - queryWithText() with embedding function overrides and filters
 * - Collections pagination (list with limit and offset parameters)
 */
beforeEach(function () {
    // Create client matching README setup
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000',
        tenant: 'default_tenant',
        database: 'default_database'
    );

    // Create a mock embedding function for testing (similar to README example)
    $this->mockEmbedder = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            // Return test embeddings with 128 dimensions
            // Each document gets a slightly different embedding for testing
            return array_map(
                fn ($text, $index) => createTestVector(0.1 + ($index * 0.01), 128),
                $texts,
                array_keys($texts)
            );
        }
    };

    // Reset server to clean state before each test
    $this->chromadb->server()->reset();
});

afterEach(function () {
    // Clean up by resetting server
    $this->chromadb->server()->reset();
});

test('README example: addWithEmbeddings with all parameters', function () {
    // This test verifies the README example:
    // $chromadb->items()->addWithEmbeddings(
    //     collectionId: $collectionId,
    //     documents: ['Document 1', 'Document 2'],
    //     embeddingFunction: null,  // Optional: override default embedding function
    //     ids: ['doc1', 'doc2'],    // Optional: auto-generated if null
    //     metadatas: [['type' => 'article'], ['type' => 'blog']]  // Optional
    // );

    // Step 1: Create a collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_example_collection'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Step 2: Configure client with embedder (as shown in README)
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Step 3: Add documents with embeddings using all parameters
    $addResponse = $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['Document 1', 'Document 2'],
        embeddingFunction: null,  // Relying on configured embedder
        ids: ['doc1', 'doc2'],
        metadatas: [['type' => 'article'], ['type' => 'blog']]
    );

    // Verify items were added successfully
    expect($addResponse->successful())->toBeTrue();

    // Step 4: Retrieve and verify the items
    $getResponse = $chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['doc1', 'doc2'],
        include: ['documents', 'metadatas', 'embeddings']
    );

    expect($getResponse->ok())->toBeTrue();

    $items = $getResponse->json();

    // Verify IDs match exactly what we provided
    expect($items['ids'])->toEqual(['doc1', 'doc2']);

    // Verify documents were saved correctly
    expect($items['documents'])->toEqual(['Document 1', 'Document 2']);

    // Verify metadata was saved correctly with the exact structure from README
    expect($items['metadatas'])->toHaveCount(2)
        ->and($items['metadatas'][0])->toEqual(['type' => 'article'])
        ->and($items['metadatas'][1])->toEqual(['type' => 'blog']);

    // Verify embeddings were generated (should be 128 dimensions)
    expect($items['embeddings'])->toHaveCount(2)
        ->and($items['embeddings'][0])->toHaveCount(128)
        ->and($items['embeddings'][1])->toHaveCount(128);
});

test('README example: addWithEmbeddings with explicit embedding function override', function () {
    // This tests the scenario where you override the configured embedder
    // as shown in the README queryWithText example:
    // $mistralEmbedder = new MistralEmbeddings(...);
    // $chromadb->items()->addWithEmbeddings(..., embeddingFunction: $mistralEmbedder)

    // Create collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_override_collection'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Configure client with default embedder
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Create an alternative embedder (simulating Mistral example from README)
    $alternativeEmbedder = new class implements EmbeddingFunction
    {
        public function generate(array $texts): array
        {
            // Generate different embeddings to verify override works
            return array_map(
                fn ($text, $index) => createTestVector(0.5 + ($index * 0.01), 128),
                $texts,
                array_keys($texts)
            );
        }
    };

    // Add documents with explicit embedding function override
    $addResponse = $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['Override test document'],
        embeddingFunction: $alternativeEmbedder,  // Override default embedder
        ids: ['override1']
    );

    expect($addResponse->successful())->toBeTrue();

    // Verify the item was added
    $getResponse = $chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['override1'],
        include: ['embeddings']
    );

    $items = $getResponse->json();

    // Verify embedding was generated (and is different from default embedder)
    expect($items['embeddings'][0])->toHaveCount(128)
        ->and($items['embeddings'][0][0])->toBeGreaterThan(0.4); // Different from default 0.1
});

test('README example: addWithEmbeddings with minimal parameters (documents only)', function () {
    // Test the simplest case shown in README comments:
    // Just documents, relying on auto-generated IDs and optional metadata

    // Create collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_minimal_collection'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Configure client with embedder
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Add documents with minimal parameters (no IDs, no metadata)
    $addResponse = $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['Minimal Document 1', 'Minimal Document 2', 'Minimal Document 3']
    );

    expect($addResponse->successful())->toBeTrue();

    // Verify count
    $count = $chromadb->items()->count($collectionId);
    expect($count)->toBe(3);

    // Retrieve all items
    $getResponse = $chromadb->items()->get(
        collectionId: $collectionId,
        ids: [],
        include: ['documents', 'embeddings'],
        limit: 10
    );

    $items = $getResponse->json();

    // Verify auto-generated IDs exist
    expect($items['ids'])->toHaveCount(3)
        ->and($items['ids'][0])->toBeString()->not->toBeEmpty()
        ->and($items['ids'][1])->toBeString()->not->toBeEmpty()
        ->and($items['ids'][2])->toBeString()->not->toBeEmpty();

    // Verify documents were stored
    expect($items['documents'])->toContain('Minimal Document 1')
        ->and($items['documents'])->toContain('Minimal Document 2')
        ->and($items['documents'])->toContain('Minimal Document 3');

    // Verify embeddings were generated
    expect($items['embeddings'])->toHaveCount(3)
        ->and($items['embeddings'][0])->toHaveCount(128);
});

test('README example: addWithEmbeddings with IDs but no metadata', function () {
    // Test providing IDs but omitting metadata (common use case)

    // Create collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_ids_only_collection'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Configure client with embedder
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Add documents with custom IDs but no metadata
    $addResponse = $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: ['First doc', 'Second doc'],
        embeddingFunction: null,
        ids: ['custom_id_1', 'custom_id_2'],
        metadatas: null  // Explicitly null
    );

    expect($addResponse->successful())->toBeTrue();

    // Retrieve and verify
    $getResponse = $chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['custom_id_1', 'custom_id_2'],
        include: ['documents']
    );

    $items = $getResponse->json();

    expect($items['ids'])->toEqual(['custom_id_1', 'custom_id_2'])
        ->and($items['documents'])->toEqual(['First doc', 'Second doc']);
});

test('README example: addWithEmbeddings with complex metadata', function () {
    // Test with more complex metadata structure as would be used in real applications

    // Create collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_complex_metadata_collection'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Configure client with embedder
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Add documents with complex metadata (realistic use case)
    $addResponse = $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: [
            'Machine learning article about neural networks',
            'Tutorial on vector databases',
        ],
        embeddingFunction: null,
        ids: ['article_1', 'tutorial_1'],
        metadatas: [
            [
                'type' => 'article',
                'category' => 'machine-learning',
                'published' => true,
                'views' => 1500,
            ],
            [
                'type' => 'tutorial',
                'category' => 'databases',
                'published' => false,
                'views' => 0,
            ],
        ]
    );

    expect($addResponse->successful())->toBeTrue();

    // Retrieve and verify metadata structure
    $getResponse = $chromadb->items()->get(
        collectionId: $collectionId,
        ids: ['article_1', 'tutorial_1'],
        include: ['metadatas']
    );

    $items = $getResponse->json();

    // Verify first item metadata
    expect($items['metadatas'][0])->toHaveKey('type')
        ->and($items['metadatas'][0]['type'])->toBe('article')
        ->and($items['metadatas'][0]['category'])->toBe('machine-learning')
        ->and($items['metadatas'][0]['published'])->toBe(true)
        ->and($items['metadatas'][0]['views'])->toBe(1500);

    // Verify second item metadata
    expect($items['metadatas'][1])->toHaveKey('type')
        ->and($items['metadatas'][1]['type'])->toBe('tutorial')
        ->and($items['metadatas'][1]['category'])->toBe('databases')
        ->and($items['metadatas'][1]['published'])->toBe(false)
        ->and($items['metadatas'][1]['views'])->toBe(0);
});

test('README example: full workflow with addWithEmbeddings and queryWithText', function () {
    // This test demonstrates the complete workflow shown across README examples

    // Step 1: Create collection
    $createResponse = $this->chromadb->collections()->create(
        name: 'readme_full_workflow'
    );
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Step 2: Configure client with embedder (as shown in README)
    $chromadb = $this->chromadb->withEmbeddings($this->mockEmbedder);

    // Step 3: Add multiple documents with metadata (README example pattern)
    $chromadb->items()->addWithEmbeddings(
        collectionId: $collectionId,
        documents: [
            'Laravel framework for web development',
            'React library for building user interfaces',
            'Python for data science and machine learning',
            'PostgreSQL relational database',
        ],
        embeddingFunction: null,
        ids: ['doc1', 'doc2', 'doc3', 'doc4'],
        metadatas: [
            ['type' => 'article', 'category' => 'php'],
            ['type' => 'article', 'category' => 'javascript'],
            ['type' => 'blog', 'category' => 'python'],
            ['type' => 'blog', 'category' => 'database'],
        ]
    );

    // Step 4: Verify all items were added
    $count = $chromadb->items()->count($collectionId);
    expect($count)->toBe(4);

    // Step 5: Query with text (as shown in README)
    $queryResponse = $chromadb->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'web development frameworks',
        embeddingFunction: null,  // Uses configured embedder
        nResults: 2,
        include: ['documents', 'metadatas', 'distances']
    );

    expect($queryResponse->ok())->toBeTrue();

    $results = $queryResponse->json();

    // Verify query results structure
    expect($results)->toHaveKey('ids')
        ->and($results)->toHaveKey('documents')
        ->and($results)->toHaveKey('metadatas')
        ->and($results)->toHaveKey('distances')
        ->and($results['ids'][0])->toHaveCount(2)  // nResults=2
        ->and($results['documents'][0])->toHaveCount(2)
        ->and($results['metadatas'][0])->toHaveCount(2);

    // Step 6: Query with metadata filter (README filtering example)
    $filteredResponse = $chromadb->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'programming',
        embeddingFunction: null,
        nResults: 5,
        where: ['type' => 'article'],  // Filter by metadata
        include: ['documents', 'metadatas']
    );

    expect($filteredResponse->ok())->toBeTrue();

    $filteredResults = $filteredResponse->json();

    // All results should have type='article'
    foreach ($filteredResults['metadatas'][0] as $metadata) {
        expect($metadata['type'])->toBe('article');
    }
});

it('queryWithText() works with Mistral embedding override and filters', function () {
    // Create a collection
    $createResponse = $this->chromadb->collections()->create('tech_articles');
    expect($createResponse->ok())->toBeTrue();
    $collectionId = $createResponse->json('id');

    // Add several items with different metadata and documents
    $addResponse = $this->chromadb->items()->add(
        collectionId: $collectionId,
        ids: ['item1', 'item2', 'item3'],
        embeddings: [
            [0.1, 0.2, 0.3, 0.4, 0.5], // machine learning and AI
            [0.2, 0.3, 0.4, 0.5, 0.6], // programming basics
            [0.3, 0.4, 0.5, 0.6, 0.7], // machine learning applications
        ],
        documents: [
            'machine learning and AI',
            'programming basics',
            'machine learning applications',
        ],
        metadatas: [
            ['category' => 'tech'],
            ['category' => 'tech'],
            ['category' => 'business'],
        ]
    );

    expect($addResponse->successful())->toBeTrue();

    // Create a mock MistralEmbeddings instance that returns test vectors
    // This doesn't require a real API key
    $mockMistralEmbedder = new class implements EmbeddingFunction
    {
        /**
         * Generate mock embeddings for testing.
         *
         * @param  array<string>  $texts
         * @return array<array<float>>
         */
        public function generate(array $texts): array
        {
            $embeddings = [];
            foreach ($texts as $text) {
                // Generate a simple test vector based on text content
                // For 'machine learning' query, return vector similar to item1
                $embeddings[] = [0.1, 0.2, 0.3, 0.4, 0.5];
            }

            return $embeddings;
        }
    };

    // Call queryWithText() with custom embedder and filters
    $queryResponse = $this->chromadb->items()->queryWithText(
        collectionId: $collectionId,
        queryText: 'machine learning',
        embeddingFunction: $mockMistralEmbedder, // Override default embedder
        nResults: 10,
        include: ['documents', 'metadatas', 'distances'],
        where: ['category' => 'tech'], // Filter by category
        whereDocument: ['$contains' => 'AI'] // Filter by document content
    );

    expect($queryResponse->ok())->toBeTrue();

    // Verify results structure
    $results = $queryResponse->json();
    expect($results)->toHaveKeys(['ids', 'documents', 'metadatas', 'distances']);

    // Verify only items matching filters are returned
    // Should return item1 only (category='tech' AND document contains 'AI')
    expect($results['ids'])->toBeArray()
        ->and($results['ids'][0])->toBeArray()
        ->and($results['ids'][0])->toContain('item1')
        ->and($results['ids'][0])->not->toContain('item2') // Filtered out (no 'AI' in doc)
        ->and($results['ids'][0])->not->toContain('item3'); // Filtered out (category='business')

    // Verify documents are included
    expect($results['documents'])->toBeArray()
        ->and($results['documents'][0])->toBeArray()
        ->and($results['documents'][0])->toContain('machine learning and AI');

    // Verify metadatas are included
    expect($results['metadatas'])->toBeArray()
        ->and($results['metadatas'][0])->toBeArray()
        ->and($results['metadatas'][0][0])->toHaveKey('category')
        ->and($results['metadatas'][0][0]['category'])->toBe('tech');

    // Verify distances are included
    expect($results['distances'])->toBeArray()
        ->and($results['distances'][0])->toBeArray()
        ->and($results['distances'][0][0])->toBeFloat();

    // Verify the custom embedder was used by checking that we got results
    // If embedder wasn't used, we'd get an error about no embedding function configured
    expect(count($results['ids'][0]))->toBeGreaterThan(0);
});

it('lists collections with pagination', function () {
    // Create 15 test collections (collection_1 through collection_15)
    $collectionIds = [];
    for ($i = 1; $i <= 15; $i++) {
        $response = $this->chromadb->collections()->create(
            name: "collection_{$i}"
        );
        expect($response->ok())->toBeTrue();
        $collectionIds[] = $response->json('id');
    }

    // Test basic list (no parameters) - should return all 15 collections
    $allResponse = $this->chromadb->collections()->list();
    expect($allResponse->ok())->toBeTrue();

    $allCollections = $allResponse->json();
    expect($allCollections)->toBeArray()
        ->and(count($allCollections))->toBe(15);

    // Test pagination: limit 10, offset 0 - should return first 10 collections
    $firstPageResponse = $this->chromadb->collections()->list(
        limit: 10,
        offset: 0
    );
    expect($firstPageResponse->ok())->toBeTrue();

    $firstPageCollections = $firstPageResponse->json();
    expect($firstPageCollections)->toBeArray()
        ->and(count($firstPageCollections))->toBe(10);

    // Test pagination: limit 10, offset 10 - should return remaining 5 collections
    $secondPageResponse = $this->chromadb->collections()->list(
        limit: 10,
        offset: 10
    );
    expect($secondPageResponse->ok())->toBeTrue();

    $secondPageCollections = $secondPageResponse->json();
    expect($secondPageCollections)->toBeArray()
        ->and(count($secondPageCollections))->toBe(5);

    // Test pagination: limit 5, offset 5 - should return collections 6-10
    $middlePageResponse = $this->chromadb->collections()->list(
        limit: 5,
        offset: 5
    );
    expect($middlePageResponse->ok())->toBeTrue();

    $middlePageCollections = $middlePageResponse->json();
    expect($middlePageCollections)->toBeArray()
        ->and(count($middlePageCollections))->toBe(5);

    // Verify no overlap between first and second page
    $firstPageNames = array_column($firstPageCollections, 'name');
    $secondPageNames = array_column($secondPageCollections, 'name');
    $intersection = array_intersect($firstPageNames, $secondPageNames);
    expect($intersection)->toBeEmpty();

    // Verify all collections are accounted for across both pages
    $combinedNames = array_merge($firstPageNames, $secondPageNames);
    expect(count($combinedNames))->toBe(15);

    // Verify each expected collection name exists in the combined results
    for ($i = 1; $i <= 15; $i++) {
        expect($combinedNames)->toContain("collection_{$i}");
    }
});
