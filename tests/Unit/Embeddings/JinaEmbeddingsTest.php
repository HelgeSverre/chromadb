<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HelgeSverre\Chromadb\Embeddings\JinaEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

function createMockedJinaEmbeddings(MockHandler $mock): JinaEmbeddings
{
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new JinaEmbeddings('test-api-key');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    return $embeddings;
}

test('can generate embeddings successfully', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockJinaResponse(2))),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2']);

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBeArray()->toHaveCount(1024)
        ->and($result[1])->toBeArray()->toHaveCount(1024);
});

test('can generate single embedding', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockJinaResponse(1))),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);
    $result = $embeddings->generate(['single text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(1024);
});

test('can use custom model', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockJinaResponse(1, 768))),
    ]);

    $embeddings = new JinaEmbeddings('test-api-key', 'jina-embeddings-v2-base-en');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $property->setValue($embeddings, $client);

    $result = $embeddings->generate(['test']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(768);
});

test('can use task parameter', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockJinaResponse(1))),
    ]);

    $embeddings = new JinaEmbeddings('test-api-key', 'jina-embeddings-v3', 'retrieval.query');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    $property->setValue($embeddings, $client);

    $result = $embeddings->generate(['query text']);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('sorts embeddings by index', function () {
    // Create response with reversed indices
    $mockResponse = [
        'data' => [
            ['embedding' => createTestVector(0.2), 'index' => 1],
            ['embedding' => createTestVector(0.1), 'index' => 0],
            ['embedding' => createTestVector(0.3), 'index' => 2],
        ],
        'model' => 'jina-embeddings-v3',
        'usage' => ['prompt_tokens' => 30, 'total_tokens' => 30],
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2', 'text 3']);

    expect($result)->toBeArray()->toHaveCount(3)
        ->and($result[0][0])->toBe(0.1) // First embedding should have value 0.1 (index 0)
        ->and($result[1][0])->toBe(0.2) // Second should have 0.2 (index 1)
        ->and($result[2][0])->toBe(0.3); // Third should have 0.3 (index 2)
});

test('throws exception on empty input', function () {
    $embeddings = new JinaEmbeddings('test-api-key');

    $embeddings->generate([]);
})->throws(EmbeddingException::class, 'Cannot generate embeddings for an empty array');

test('throws exception on authentication failure', function () {
    $mock = new MockHandler([
        new RequestException(
            'Unauthorized',
            new Request('POST', 'embeddings'),
            new Response(401, [], json_encode(['error' => ['message' => 'Invalid API key']]))
        ),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Authentication failed for Jina AI');

test('throws exception on rate limit', function () {
    $mock = new MockHandler([
        new RequestException(
            'Rate limit exceeded',
            new Request('POST', 'embeddings'),
            new Response(429, [], json_encode(['error' => ['message' => 'Rate limit exceeded']]))
        ),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Rate limit exceeded for Jina AI');

test('throws exception on server error', function () {
    $mock = new MockHandler([
        new RequestException(
            'Server error',
            new Request('POST', 'embeddings'),
            new Response(500, [], json_encode(['error' => ['message' => 'Internal server error']]))
        ),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Error calling Jina AI API');

test('throws exception on invalid response format', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['invalid' => 'response'])),
    ]);

    $embeddings = createMockedJinaEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Invalid response from Jina AI API');
