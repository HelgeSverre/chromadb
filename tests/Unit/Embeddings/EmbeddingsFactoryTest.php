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

// Configuration error scenario tests (from TESTING_TODO.md section 8)

test('fromConfig() with missing API key throws exception', function () {
    config()->set('chromadb.embeddings.providers.openai', [
        'model' => 'text-embedding-3-small',
        // Missing 'api_key'
    ]);

    expect(fn () => Embeddings::fromConfig('openai'))
        ->toThrow(InvalidArgumentException::class, 'OpenAI API key not configured');
});

test('fromConfig() with unknown provider throws exception', function () {
    expect(fn () => Embeddings::fromConfig('nonexistent-provider'))
        ->toThrow(InvalidArgumentException::class, 'No configuration found for embedding provider: nonexistent-provider');
});

test('fromConfig() with missing config file throws exception', function () {
    // Attempt to load provider with null configuration (simulating missing config)
    config()->set('chromadb.embeddings.providers.custom', null);

    expect(fn () => Embeddings::fromConfig('custom'))
        ->toThrow(InvalidArgumentException::class, 'No configuration found for embedding provider: custom');
});

test('fromConfig() loads correct provider from default config', function () {
    // Setup default provider configuration
    config()->set('chromadb.embeddings.default', 'openai');
    config()->set('chromadb.embeddings.providers.openai', [
        'api_key' => 'sk-test-key',
        'model' => 'text-embedding-3-small',
    ]);

    $embeddings = Embeddings::fromConfig();

    expect($embeddings)->toBeInstanceOf(OpenAIEmbeddings::class);
});

test('fromConfig() validates provider configuration structure', function () {
    // Test that configuration structure is properly validated for multiple providers

    // OpenAI with full config
    config()->set('chromadb.embeddings.providers.openai', [
        'api_key' => 'sk-test-key',
        'model' => 'text-embedding-3-large',
        'dimensions' => 256,
        'organization' => 'org-123',
    ]);
    $openai = Embeddings::fromConfig('openai');
    expect($openai)->toBeInstanceOf(OpenAIEmbeddings::class);

    // VoyageAI with config
    config()->set('chromadb.embeddings.providers.voyage', [
        'api_key' => 'voyage-test-key',
        'model' => 'voyage-3.5',
        'input_type' => 'document',
    ]);
    $voyage = Embeddings::fromConfig('voyage');
    expect($voyage)->toBeInstanceOf(VoyageAIEmbeddings::class);

    // Mistral with config
    config()->set('chromadb.embeddings.providers.mistral', [
        'api_key' => 'mistral-test-key',
        'model' => 'mistral-embed',
    ]);
    $mistral = Embeddings::fromConfig('mistral');
    expect($mistral)->toBeInstanceOf(MistralEmbeddings::class);

    // Jina with config
    config()->set('chromadb.embeddings.providers.jina', [
        'api_key' => 'jina-test-key',
        'model' => 'jina-embeddings-v3',
        'task' => 'retrieval.query',
        'dimensions' => 1024,
    ]);
    $jina = Embeddings::fromConfig('jina');
    expect($jina)->toBeInstanceOf(JinaEmbeddings::class);

    // Ollama with config
    config()->set('chromadb.embeddings.providers.ollama', [
        'model' => 'all-minilm',
        'base_url' => 'http://localhost:11434',
    ]);
    $ollama = Embeddings::fromConfig('ollama');
    expect($ollama)->toBeInstanceOf(OllamaEmbeddings::class);
});
