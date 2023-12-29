<?php

namespace HelgeSverre\Chromadb;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use SensitiveParameter;

class Chromadb extends Connector
{
    public function __construct(
        #[SensitiveParameter]
        protected readonly ?string $token,
        protected string $host = 'http://localhost',
        protected string $port = '8080',
    ) {
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    public function resolveBaseUrl(): string
    {
        return "{$this->host}:{$this->port}";
    }

    // Resources

    public function collections()
    {
        // TODO: implement
    }

    public function items()
    {
        // TODO: implement
    }

    public function database()
    {
        // TODO: implement
    }

    public function tenant()
    {
        // TODO: implement
    }

    public function server()
    {
        // TODO: implement
    }
}
