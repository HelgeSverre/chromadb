<?php

use HelgeSverre\Chromadb\Embeddings\MistralEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

beforeEach(function () {
    $apiKey = env('MISTRAL_API_KEY');

    if (! $apiKey) {
        $this->markTestSkipped('MISTRAL_API_KEY environment variable not set. Set it to run Mistral integration tests.');
    }

    $this->apiKey = $apiKey;
});

test('can generate real embeddings from Mistral API', function () {
    $embeddings = new MistralEmbeddings($this->apiKey);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'mistral');

test('can generate multiple embeddings', function () {
    $embeddings = new MistralEmbeddings($this->apiKey);

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
})->group('integration', 'mistral');

test('generates embeddings with expected dimensions', function () {
    $embeddings = new MistralEmbeddings($this->apiKey);

    $result = $embeddings->generate(['Test text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and(count($result[0]))->toBe(1024); // mistral-embed produces 1024-dimensional embeddings
})->group('integration', 'mistral');

test('throws exception with invalid API key', function () {
    $embeddings = new MistralEmbeddings('invalid-api-key-12345');

    $embeddings->generate(['This should fail']);
})->throws(EmbeddingException::class)->group('integration', 'mistral');
