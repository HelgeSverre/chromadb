<?php

use HelgeSverre\Chromadb\Embeddings\VoyageAIEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

beforeEach(function () {
    $apiKey = env('VOYAGE_API_KEY');

    if (! $apiKey) {
        $this->markTestSkipped('VOYAGE_API_KEY environment variable not set. Set it to run VoyageAI integration tests.');
    }

    $this->apiKey = $apiKey;
});

test('can generate real embeddings from VoyageAI API', function () {
    $embeddings = new VoyageAIEmbeddings($this->apiKey);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'voyageai');

test('can generate multiple embeddings', function () {
    $embeddings = new VoyageAIEmbeddings($this->apiKey);

    $result = $embeddings->generate([
        'The quick brown fox',
        'jumps over the lazy dog',
        'Machine learning is fascinating',
    ]);

    expect($result)->toBeArray()
        ->toHaveCount(3)
        ->and($result[0])->toBeArray()
        ->and($result[1])->toBeArray()
        ->and($result[2])->toBeArray();
})->group('integration', 'voyageai');

test('can use document input type', function () {
    $embeddings = new VoyageAIEmbeddings($this->apiKey, 'voyage-3', 'document');

    $result = $embeddings->generate(['This is a document to be embedded']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray();
})->group('integration', 'voyageai');

test('can use query input type', function () {
    $embeddings = new VoyageAIEmbeddings($this->apiKey, 'voyage-3', 'query');

    $result = $embeddings->generate(['What is machine learning?']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray();
})->group('integration', 'voyageai');

test('throws exception with invalid API key', function () {
    $embeddings = new VoyageAIEmbeddings('invalid-api-key-12345');

    $embeddings->generate(['This should fail']);
})->throws(EmbeddingException::class)->group('integration', 'voyageai');
