<?php

use function HelgeSverre\Chromadb\Embeddings\embeddings_jina;
use function HelgeSverre\Chromadb\Embeddings\embeddings_mistral;
use function HelgeSverre\Chromadb\Embeddings\embeddings_ollama;
use function HelgeSverre\Chromadb\Embeddings\embeddings_openai;
use function HelgeSverre\Chromadb\Embeddings\embeddings_voyage;

use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

test('embeddings_openai helper function exists', function () {
    expect(function_exists('HelgeSverre\Chromadb\Embeddings\embeddings_openai'))->toBeTrue();
});

test('embeddings_openai throws exception for empty input', function () {
    embeddings_openai([], 'fake-key');
})->throws(EmbeddingException::class);

test('embeddings_voyage helper function exists', function () {
    expect(function_exists('HelgeSverre\Chromadb\Embeddings\embeddings_voyage'))->toBeTrue();
});

test('embeddings_voyage throws exception for empty input', function () {
    embeddings_voyage([], 'fake-key');
})->throws(EmbeddingException::class);

test('embeddings_mistral helper function exists', function () {
    expect(function_exists('HelgeSverre\Chromadb\Embeddings\embeddings_mistral'))->toBeTrue();
});

test('embeddings_mistral throws exception for empty input', function () {
    embeddings_mistral([], 'fake-key');
})->throws(EmbeddingException::class);

test('embeddings_jina helper function exists', function () {
    expect(function_exists('HelgeSverre\Chromadb\Embeddings\embeddings_jina'))->toBeTrue();
});

test('embeddings_jina throws exception for empty input', function () {
    embeddings_jina([], 'fake-key');
})->throws(EmbeddingException::class);

test('embeddings_ollama helper function exists', function () {
    expect(function_exists('HelgeSverre\Chromadb\Embeddings\embeddings_ollama'))->toBeTrue();
});

test('embeddings_ollama throws exception for empty input', function () {
    embeddings_ollama([]);
})->throws(EmbeddingException::class);
