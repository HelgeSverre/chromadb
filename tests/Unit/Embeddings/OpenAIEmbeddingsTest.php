<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HelgeSverre\Chromadb\Embeddings\OpenAIEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

function createMockedOpenAIEmbeddings(MockHandler $mock): OpenAIEmbeddings
{
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new OpenAIEmbeddings('test-api-key');

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    return $embeddings;
}

test('can generate embeddings successfully', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOpenAIResponse(2))),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2']);

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBeArray()->toHaveCount(1536)
        ->and($result[1])->toBeArray()->toHaveCount(1536);
});

test('can generate single embedding', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOpenAIResponse(1))),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);
    $result = $embeddings->generate(['single text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(1536);
});

test('can handle custom dimensions', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockOpenAIResponse(1, 512))),
    ]);

    $embeddings = new OpenAIEmbeddings('test-api-key', 'text-embedding-3-small', 512);

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
        ->and($result[0])->toBeArray()->toHaveCount(512);
});

test('sorts embeddings by index', function () {
    // Create response with reversed indices
    $mockResponse = [
        'data' => [
            ['embedding' => createTestVector(0.2), 'index' => 1],
            ['embedding' => createTestVector(0.1), 'index' => 0],
            ['embedding' => createTestVector(0.3), 'index' => 2],
        ],
        'model' => 'text-embedding-3-small',
        'usage' => ['prompt_tokens' => 30, 'total_tokens' => 30],
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2', 'text 3']);

    expect($result)->toBeArray()->toHaveCount(3)
        ->and($result[0][0])->toBe(0.1) // First embedding should have value 0.1 (index 0)
        ->and($result[1][0])->toBe(0.2) // Second should have 0.2 (index 1)
        ->and($result[2][0])->toBe(0.3); // Third should have 0.3 (index 2)
});

test('throws exception on empty input', function () {
    $embeddings = new OpenAIEmbeddings('test-api-key');

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

    $embeddings = createMockedOpenAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Authentication failed for OpenAI');

test('throws exception on rate limit', function () {
    $mock = new MockHandler([
        new RequestException(
            'Rate limit exceeded',
            new Request('POST', 'embeddings'),
            new Response(429, [], json_encode(['error' => ['message' => 'Rate limit exceeded']]))
        ),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Rate limit exceeded for OpenAI');

test('throws exception on server error', function () {
    $mock = new MockHandler([
        new RequestException(
            'Server error',
            new Request('POST', 'embeddings'),
            new Response(500, [], json_encode(['error' => ['message' => 'Internal server error']]))
        ),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Error calling OpenAI API');

test('throws exception on invalid response format', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['invalid' => 'response'])),
    ]);

    $embeddings = createMockedOpenAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Invalid response from OpenAI API');
