<?php

use HelgeSverre\Chromadb\Embeddings\Embeddings;
use HelgeSverre\Chromadb\Embeddings\JinaEmbeddings;
use HelgeSverre\Chromadb\Embeddings\MistralEmbeddings;
use HelgeSverre\Chromadb\Embeddings\OllamaEmbeddings;
use HelgeSverre\Chromadb\Embeddings\OpenAIEmbeddings;
use HelgeSverre\Chromadb\Embeddings\VoyageAIEmbeddings;

test('can create OpenAI embeddings instance', function () {
    $embeddings = Embeddings::openai('test-api-key');

    expect($embeddings)->toBeInstanceOf(OpenAIEmbeddings::class);
});

test('can create OpenAI embeddings with custom parameters', function () {
    $embeddings = Embeddings::openai(
        'test-api-key',
        'text-embedding-3-large',
        256,
        'org-123'
    );

    expect($embeddings)->toBeInstanceOf(OpenAIEmbeddings::class);
});

test('can create VoyageAI embeddings instance', function () {
    $embeddings = Embeddings::voyage('test-api-key');

    expect($embeddings)->toBeInstanceOf(VoyageAIEmbeddings::class);
});

test('can create VoyageAI embeddings with custom parameters', function () {
    $embeddings = Embeddings::voyage('test-api-key', 'voyage-3-large', 'query');

    expect($embeddings)->toBeInstanceOf(VoyageAIEmbeddings::class);
});

test('can create Mistral embeddings instance', function () {
    $embeddings = Embeddings::mistral('test-api-key');

    expect($embeddings)->toBeInstanceOf(MistralEmbeddings::class);
});

test('can create Mistral embeddings with custom model', function () {
    $embeddings = Embeddings::mistral('test-api-key', 'mistral-embed-v2');

    expect($embeddings)->toBeInstanceOf(MistralEmbeddings::class);
});

test('can create Jina AI embeddings instance', function () {
    $embeddings = Embeddings::jina('test-api-key');

    expect($embeddings)->toBeInstanceOf(JinaEmbeddings::class);
});

test('can create Jina AI embeddings with custom parameters', function () {
    $embeddings = Embeddings::jina('test-api-key', 'jina-embeddings-v2-base-en', 'retrieval.query', 768);

    expect($embeddings)->toBeInstanceOf(JinaEmbeddings::class);
});

test('can create Ollama embeddings instance', function () {
    $embeddings = Embeddings::ollama();

    expect($embeddings)->toBeInstanceOf(OllamaEmbeddings::class);
});

test('can create Ollama embeddings with custom parameters', function () {
    $embeddings = Embeddings::ollama('nomic-embed-text', 'http://custom:11434');

    expect($embeddings)->toBeInstanceOf(OllamaEmbeddings::class);
});
