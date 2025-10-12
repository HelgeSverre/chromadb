<?php

use HelgeSverre\Chromadb\Embeddings\OpenAIEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

beforeEach(function () {
    $apiKey = env('OPENAI_API_KEY');

    if (! $apiKey) {
        $this->markTestSkipped('OPENAI_API_KEY environment variable not set. Set it to run OpenAI integration tests.');
    }

    $this->apiKey = $apiKey;
});

test('can generate real embeddings from OpenAI API', function () {
    $embeddings = new OpenAIEmbeddings($this->apiKey);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'openai');

test('can generate multiple embeddings', function () {
    $embeddings = new OpenAIEmbeddings($this->apiKey);

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
})->group('integration', 'openai');

test('can use text-embedding-3-small model', function () {
    $embeddings = new OpenAIEmbeddings($this->apiKey, 'text-embedding-3-small');

    $result = $embeddings->generate(['Test text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and(count($result[0]))->toBe(1536); // Default dimensions for text-embedding-3-small
})->group('integration', 'openai');

test('can use custom dimensions', function () {
    $embeddings = new OpenAIEmbeddings($this->apiKey, 'text-embedding-3-small', 512);

    $result = $embeddings->generate(['Test text with custom dimensions']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and(count($result[0]))->toBe(512);
})->group('integration', 'openai');

test('throws exception with invalid API key', function () {
    $embeddings = new OpenAIEmbeddings('invalid-api-key-12345');

    $embeddings->generate(['This should fail']);
})->throws(EmbeddingException::class)->group('integration', 'openai');
