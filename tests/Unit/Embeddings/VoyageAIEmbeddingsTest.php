<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HelgeSverre\Chromadb\Embeddings\VoyageAIEmbeddings;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

function createMockedVoyageAIEmbeddings(MockHandler $mock, string $inputType = 'document'): VoyageAIEmbeddings
{
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $embeddings = new VoyageAIEmbeddings('test-api-key', 'voyage-3.5', $inputType);

    // Use reflection to inject the mocked client
    $reflection = new ReflectionClass($embeddings);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($embeddings, $client);

    return $embeddings;
}

test('can generate embeddings successfully', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockVoyageResponse(2))),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2']);

    expect($result)->toBeArray()
        ->toHaveCount(2)
        ->and($result[0])->toBeArray()->toHaveCount(1024)
        ->and($result[1])->toBeArray()->toHaveCount(1024);
});

test('can generate single embedding', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockVoyageResponse(1))),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock);
    $result = $embeddings->generate(['single text']);

    expect($result)->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])->toBeArray()->toHaveCount(1024);
});

test('can use document input type', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockVoyageResponse(1))),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock, 'document');
    $result = $embeddings->generate(['document text']);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('can use query input type', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(mockVoyageResponse(1))),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock, 'query');
    $result = $embeddings->generate(['query text']);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('sorts embeddings by index', function () {
    // Create response with reversed indices
    $mockResponse = [
        'data' => [
            ['embedding' => createTestVector(0.2), 'index' => 1],
            ['embedding' => createTestVector(0.1), 'index' => 0],
        ],
        'model' => 'voyage-3.5',
        'usage' => ['total_tokens' => 20],
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock);
    $result = $embeddings->generate(['text 1', 'text 2']);

    expect($result)->toBeArray()->toHaveCount(2)
        ->and($result[0][0])->toBe(0.1) // First embedding should have value 0.1 (index 0)
        ->and($result[1][0])->toBe(0.2); // Second should have 0.2 (index 1)
});

test('throws exception on empty input', function () {
    $embeddings = new VoyageAIEmbeddings('test-api-key');

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

    $embeddings = createMockedVoyageAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Authentication failed for VoyageAI');

test('throws exception on rate limit', function () {
    $mock = new MockHandler([
        new RequestException(
            'Rate limit exceeded',
            new Request('POST', 'embeddings'),
            new Response(429, [], json_encode(['error' => ['message' => 'Rate limit exceeded']]))
        ),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Rate limit exceeded for VoyageAI');

test('throws exception on invalid response format', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['invalid' => 'response'])),
    ]);

    $embeddings = createMockedVoyageAIEmbeddings($mock);

    $embeddings->generate(['test']);
})->throws(EmbeddingException::class, 'Invalid response from VoyageAI API');
