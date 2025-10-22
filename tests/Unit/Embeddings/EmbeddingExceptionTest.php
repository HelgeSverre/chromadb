<?php

use HelgeSverre\Chromadb\Exceptions\EmbeddingException;

test('can create apiError exception', function () {
    $exception = EmbeddingException::apiError('TestProvider', 'Something went wrong');

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Error calling TestProvider API: Something went wrong');
});

test('can create apiError exception with previous exception', function () {
    $previous = new RuntimeException('Previous error');
    $exception = EmbeddingException::apiError('TestProvider', 'Something went wrong', $previous);

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Error calling TestProvider API: Something went wrong')
        ->and($exception->getPrevious())->toBe($previous);
});

test('can create invalidResponse exception', function () {
    $exception = EmbeddingException::invalidResponse('TestProvider');

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Invalid response from TestProvider API');
});

test('can create invalidResponse exception with details', function () {
    $exception = EmbeddingException::invalidResponse('TestProvider', 'Missing data field');

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Invalid response from TestProvider API: Missing data field');
});

test('can create authenticationFailed exception', function () {
    $exception = EmbeddingException::authenticationFailed('TestProvider');

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Authentication failed for TestProvider API. Please check your API key.');
});

test('can create rateLimitExceeded exception', function () {
    $exception = EmbeddingException::rateLimitExceeded('TestProvider');

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Rate limit exceeded for TestProvider API');
});

test('can create rateLimitExceeded exception with retry after', function () {
    $exception = EmbeddingException::rateLimitExceeded('TestProvider', 60);

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Rate limit exceeded for TestProvider API. Retry after 60 seconds');
});

test('can create emptyInput exception', function () {
    $exception = EmbeddingException::emptyInput();

    expect($exception)->toBeInstanceOf(EmbeddingException::class)
        ->and($exception->getMessage())->toBe('Cannot generate embeddings for an empty array of texts');
});

test('all exceptions extend RuntimeException', function () {
    expect(EmbeddingException::emptyInput())->toBeInstanceOf(RuntimeException::class)
        ->and(EmbeddingException::apiError('Test', 'error'))->toBeInstanceOf(RuntimeException::class)
        ->and(EmbeddingException::invalidResponse('Test'))->toBeInstanceOf(RuntimeException::class)
        ->and(EmbeddingException::authenticationFailed('Test'))->toBeInstanceOf(RuntimeException::class)
        ->and(EmbeddingException::rateLimitExceeded('Test'))->toBeInstanceOf(RuntimeException::class);
});
