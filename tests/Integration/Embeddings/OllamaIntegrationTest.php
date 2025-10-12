<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HelgeSverre\Chromadb\Embeddings\OllamaEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

function isOllamaRunning(string $baseUrl = 'http://localhost:11434'): bool
{
    try {
        $client = new Client(['timeout' => 2]);
        $response = $client->get($baseUrl);

        return $response->getStatusCode() === 200;
    } catch (GuzzleException) {
        return false;
    }
}

beforeEach(function () {
    $baseUrl = env('OLLAMA_BASE_URL') ?: 'http://localhost:11434';

    if (! isOllamaRunning($baseUrl)) {
        $this->markTestSkipped("Ollama is not running at {$baseUrl}. Start Ollama to run integration tests.");
    }

    $this->baseUrl = $baseUrl;
});

// $ ollama pull all-minilm
test('can generate real embeddings from Ollama - using all-minilm', function () {
    $embeddings = new OllamaEmbeddings('all-minilm', $this->baseUrl);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'ollama');

// $ ollama pull embeddinggemma
test('can generate real embeddings from Ollama - using embeddinggemma', function () {

    $embeddings = new OllamaEmbeddings('embeddinggemma', $this->baseUrl);

    $result = $embeddings->generate(['Hello, world!']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()
        ->and(count($result[0]))->toBeGreaterThan(0);
})->group('integration', 'ollama');

test('will fail if you use an invalid model', function () {

    $embeddings = new OllamaEmbeddings('not-real-embedding-model', $this->baseUrl);

    $result = $embeddings->generate(['Hello, world!']);
})->throws(EmbeddingException::class)->group('integration', 'ollama');

test('can generate multiple embeddings', function () {
    $embeddings = new OllamaEmbeddings('all-minilm', $this->baseUrl);

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
})->group('integration', 'ollama');

test('can use default model', function () {
    $embeddings = new OllamaEmbeddings(baseUrl: $this->baseUrl);

    $result = $embeddings->generate(['Test with default model']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray();
})->group('integration', 'ollama');

test('throws exception with non-existent model', function () {
    $embeddings = new OllamaEmbeddings('non-existent-model-xyz', $this->baseUrl);

    $embeddings->generate(['This should fail']);
})->throws(EmbeddingException::class)->group('integration', 'ollama');
