<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    // Reset the server before each test
    $this->chromadb->server()->reset();
});

it('creates a database correctly', function () {
    $createResponse = $this->chromadb->database()->create('test_database');
    expect($createResponse->ok())->toBeTrue();
    expect($createResponse->json('name'))->toEqual('test_database');
});

it('retrieves a database correctly', function () {
    $this->chromadb->database()->create('test_database');
    $getResponse = $this->chromadb->database()->get('test_database');
    expect($getResponse->ok())->toBeTrue();
    expect($getResponse->json('name'))->toEqual('test_database');
});

// Add more tests as needed for different scenarios and edge cases.
