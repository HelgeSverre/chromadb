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

    expect($success)->toEqual(200);
});

it('can retrieve the server version', function () {
    $version = $this->chromadb->server()->version();
    expect($version)->toEqual('1.0.0');
});
