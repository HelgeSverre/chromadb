<?php

use HelgeSverre\Chromadb\Embeddings\JinaEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

beforeEach(function () {
    $apiKey = env('JINA_API_KEY');

    if (! $apiKey) {
        $this->markTestSkipped('JINA_API_KEY environment variable not set. Set it to run Jina AI integration tests.');
    }

    $this->apiKey = $apiKey;
});

test('can generate real embeddings from Jina AI API', function () {
    $embeddings = new JinaEmbeddings($this->apiKey);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'jina');

test('can generate multiple embeddings', function () {
    $embeddings = new JinaEmbeddings($this->apiKey);

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
})->group('integration', 'jina');

test('can use jina-embeddings-v3 model', function () {
    $embeddings = new JinaEmbeddings($this->apiKey, 'jina-embeddings-v3');

    $result = $embeddings->generate(['Test text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and(count($result[0]))->toBe(1024); // jina-embeddings-v3 produces 1024-dimensional embeddings
})->group('integration', 'jina');

test('can use task parameter for retrieval', function () {
    $embeddings = new JinaEmbeddings($this->apiKey, 'jina-embeddings-v3', 'retrieval.query');

    $result = $embeddings->generate(['What is machine learning?']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray();
})->group('integration', 'jina');

test('throws exception with invalid API key', function () {
    $embeddings = new JinaEmbeddings('invalid-api-key-12345');

    $embeddings->generate(['This should fail']);
})->throws(EmbeddingException::class)->group('integration', 'jina');
