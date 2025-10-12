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

it('deletes a database successfully', function () {
    // Create a database
    $this->chromadb->database()->create('test_delete_db');

    // Verify it exists
    $getResponse = $this->chromadb->database()->get('test_delete_db');
    expect($getResponse->ok())->toBeTrue();

    // Delete it
    $deleteResponse = $this->chromadb->database()->delete('test_delete_db');
    expect($deleteResponse->ok())->toBeTrue();

    // Verify it's gone (should return 404 or error)
    $getAgainResponse = $this->chromadb->database()->get('test_delete_db');
    expect($getAgainResponse->status())->toEqual(404);
});

it('handles deleting non-existent database gracefully', function () {
    $deleteResponse = $this->chromadb->database()->delete('non_existent_db');

    // Should return 404 or similar error
    expect($deleteResponse->status())->toBeIn([404, 400]);
});
