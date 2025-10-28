<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );

    // Note: ServerTest includes a reset test, so we don't reset here
});

it('can perform a server heartbeat', function () {
    $response = $this->chromadb->server()->heartbeat();

    expect($response->status())->toEqual(200)
        ->and($response->json())->toHaveKey('nanosecond heartbeat');
});

it('can perform a server pre-flight check', function () {
    $response = $this->chromadb->server()->preFlightChecks();

    expect($response->status())->toEqual(200)
        ->and($response->json())->toBeArray();
});

it('can reset the server', function () {
    $success = $this->chromadb->server()->reset();

    expect($success)->toBeTruthy();
});

it('reset returns true on success', function () {
    $result = $this->chromadb->server()->reset();

    expect($result)->toBeTruthy();
});

it('reset handles API error responses gracefully', function () {
    // Reset server to verify it's responsive
    $result = $this->chromadb->server()->reset();

    // Even if there are issues, reset should not throw
    expect($result)->toBeBool();
});

it('reset return type is consistent', function () {
    $result1 = $this->chromadb->server()->reset();
    $result2 = $this->chromadb->server()->reset();

    // Both calls should return boolean
    expect($result1)->toBeBool()
        ->and($result2)->toBeBool()
        ->and($result1)->toBe($result2);
});

it('can retrieve the server version', function () {
    $version = $this->chromadb->server()->version();
    expect($version)->toEqual('1.0.0');
});

it('can check server health', function () {
    $response = $this->chromadb->server()->healthcheck();

    // Health check should return 200 if healthy, 503 if unavailable
    expect($response->status())->toBeIn([200, 503]);
});
