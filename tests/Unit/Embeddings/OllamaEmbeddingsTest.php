<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HelgeSverre\Chromadb\Embeddings\OllamaEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

function createMockedOllamaEmbeddings(MockHandler $mock): OllamaEmbeddings
{
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new OllamaEmbeddings;

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    return $embeddings;
}

test('can generate embeddings successfully', function () {
    // Ollama generates embeddings one at a time
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOllamaResponse())),
        new Response(200, [], json_encode(mockOllamaResponse())),
    ]);

    $embeddings = createMockedOllamaEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2']);

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBeArray()->toHaveCount(384)
        ->and($result[1])->toBeArray()->toHaveCount(384);
});

test('can generate single embedding', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOllamaResponse())),
    ]);

    $embeddings = createMockedOllamaEmbeddings($mock);
    $result = $embeddings->generate(['single text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(384);
});

test('can use custom model', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOllamaResponse(768))),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new OllamaEmbeddings('nomic-embed-text');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    $result = $embeddings->generate(['test']);

    expect($result)->toBeArray()->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(768);
});

test('can use custom base URL', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOllamaResponse())),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new OllamaEmbeddings('all-minilm', 'http://custom:11434');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    $result = $embeddings->generate(['test']);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('throws exception on empty input', function () {
    $embeddings = new OllamaEmbeddings;

    $embeddings->generate([]);
})->throws(EmbeddingException::class, 'Cannot generate embeddings for an empty array');

test('throws exception on connection failure', function () {
    $mock = new MockHandler([
        new RequestException(
            'Connection refused',
            new Request('POST', '/api/embed')
        ),
    ]);

    $embeddings = createMockedOllamaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Error calling Ollama API');

test('throws exception on invalid response format', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['invalid' => 'response'])),
    ]);

    $embeddings = createMockedOllamaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Invalid response from Ollama API');
