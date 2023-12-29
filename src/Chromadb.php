<?php

namespace HelgeSverre\Chromadb;

use HelgeSverre\Chromadb\Resources\Collections;
use HelgeSverre\Chromadb\Resources\Database;
use HelgeSverre\Chromadb\Resources\Items;
use HelgeSverre\Chromadb\Resources\Server;
use HelgeSverre\Chromadb\Resources\Tenant;
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

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function resolveBaseUrl(): string
    {
        return "{$this->host}:{$this->port}";
    }

    // Resources
    public function collections(): Collections
    {
        return new Collections($this);
    }

    public function items(): Items
    {
        return new Items($this);
    }

    public function database(): Database
    {
        return new Database($this);
    }

    public function tenant(): Tenant
    {
        return new Tenant($this);
    }

    public function server(): Server
    {
        return new Server($this);
    }
}
