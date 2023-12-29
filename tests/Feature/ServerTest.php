<?php

use HelgeSverre\Chromadb\Chromadb;

beforeEach(function () {
    $this->chromadb = new Chromadb(
        token: 'test-token-chroma-local-dev',
        host: 'http://localhost',
        port: '8000'
    );
});

it('can perform a server heartbeat', function () {
    $response = $this->chromadb->server()->heartbeat();
    expect($response->status())->toEqual(200);
});

it('can perform a server pre-flight check', function () {
    $response = $this->chromadb->server()->preFlightChecks();
    expect($response->status())->toEqual(200);
});

it('can reset the server', function () {
    $response = $this->chromadb->server()->reset();
    expect($response->status())->toEqual(200);
});

it('can retrieve the server version', function () {
    $response = $this->chromadb->server()->version();
    expect($response->status())->toEqual(200);
    expect($response->json())->toHaveKey('version');
});
