<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $this->chromadb->server()->reset();
});

it('performs semantic search correctly', function () {
    // Prepare data (v2 API metadata cannot contain arrays, only scalar values)
    $blogPosts = [
        [
            'title' => 'Exploring Laravel',
            'summary' => 'A deep dive into Laravel frameworks...',
            'tags' => 'PHP, Laravel, Web Development', // Changed to string
        ],
        [
            'title' => 'Introduction to React',
            'summary' => 'Understanding the basics of React and how it revolutionizes frontend development.',
            'tags' => 'JavaScript, React, Frontend', // Changed to string
        ],
    ];

    // Mock OpenAI embeddings API response
    $embeddingsResponse = [
        'embeddings' => [
            ['index' => 0, 'embedding' => createTestVector(0.1, 1536)],
            ['index' => 1, 'embedding' => createTestVector(0.2, 1536)],
        ],
    ];

    foreach ($embeddingsResponse['embeddings'] as $embedding) {
        $blogPosts[$embedding['index']]['embedding'] = $embedding['embedding'];
    }

    // Create ChromaDB collection
    $createCollectionResponse = $this->chromadb->collections()->create(
        name: 'blog_posts',
    );

    $collectionId = $createCollectionResponse->json('id');

    expect($createCollectionResponse->ok())->toBeTrue();

    // Insert blog posts into ChromaDB
    foreach ($blogPosts as $post) {
        // Extract embedding separately (metadata can't contain arrays)
        $embedding = $post['embedding'];
        unset($post['embedding']);

        $addItemResponse = $this->chromadb->items()->add(
            collectionId: $collectionId,
            ids: [$post['title']],
            embeddings: [$embedding],
            metadatas: [$post]
        );

        expect($addItemResponse->successful())->toBeTrue();
    }

    // Mock search vector
    $searchEmbedding = createTestVector(0.15, 1536);

    // Perform search using ChromaDB
    $searchResponse = $this->chromadb->items()->query(
        collectionId: $collectionId,
        queryEmbeddings: [$searchEmbedding],
        include: ['metadatas'],
        nResults: 3
    );

    expect($searchResponse->ok())->toBeTrue();
    expect($searchResponse->json('ids.0'))->toHaveCount(2);
    expect($searchResponse->json('metadatas.0'))->toHaveCount(2);
    expect($searchResponse->json('metadatas.0.0'))->toHaveKey('summary');
    expect($searchResponse->json('metadatas.0.0'))->toHaveKey('title');

    // Check if the search results contain the expected titles
    $titles = array_column($searchResponse->json('metadatas.0'), 'title');
    expect($titles)->toContain('Exploring Laravel', 'Introduction to React');
});
