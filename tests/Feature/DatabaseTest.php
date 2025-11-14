<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    $this->chromadb->server()->reset();
});

it('creates a database correctly', function () {
    $createResponse = $this->chromadb->database()->create('test_database');

    expect($createResponse->ok())->toBeTrue();
});

it('retrieves a database correctly', function () {
    $this->chromadb->database()->create('test_database');
    $getResponse = $this->chromadb->database()->get('test_database');
    expect($getResponse->ok())->toBeTrue();
    expect($getResponse->json('name'))->toEqual('test_database');
});

it('lists databases correctly', function () {
    $this->chromadb->database()->create('test_db_1');
    $this->chromadb->database()->create('test_db_2');

    $response = $this->chromadb->database()->list();

    expect($response->ok())->toBeTrue()
        ->and($response->json())->toBeArray()
        ->and(count($response->json()))->toBeGreaterThanOrEqual(2);
});

it('lists databases with pagination', function () {
    // Create multiple databases
    foreach (range(1, 5) as $i) {
        $this->chromadb->database()->create("paginated_db_{$i}");
    }

    $response = $this->chromadb->database()->list(limit: 2, offset: 0);

    expect($response->ok())->toBeTrue()
        ->and($response->json())->toBeArray()
        ->and(count($response->json()))->toBeLessThanOrEqual(2);
});
