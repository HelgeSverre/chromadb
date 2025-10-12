<?php

namespace HelgeSverre\Chromadb;

use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;
use HelgeSverre\Chromadb\Resources\Collections;
use HelgeSverre\Chromadb\Resources\Database;
use HelgeSverre\Chromadb\Resources\Items;
use HelgeSverre\Chromadb\Resources\Server;
use HelgeSverre\Chromadb\Resources\Tenant;
use Saloon\Http\Connector;
use SensitiveParameter;

class Chromadb extends Connector
{
    public function __construct(
        #[SensitiveParameter]
        protected readonly ?string $token,
        protected string $host = 'http://localhost',
        protected string $port = '8000',
        protected string $tenant = 'default_tenant',
        protected string $database = 'default_database',
        protected ?EmbeddingFunction $embeddingFunction = null,
    ) {}

    /**
     * Returns the current client instance.
     * This is convenient for method chaining when accessing the class via a Facade in Laravel.
     *
     * @return $this The current client instance.
     *
     * @example
     * ```php
     * use HelgeSverre\Chromadb\Facades\Chromadb;
     * $response = Chromadb::client()->tenant()->list();
     * ```
     */
    public function client(): self
    {
        return $this;
    }

    protected function defaultHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // v2 API uses x-chroma-token header instead of Authorization
        if ($this->token) {
            $headers['x-chroma-token'] = $this->token;
        }

        return $headers;
    }

    public function resolveBaseUrl(): string
    {
        return "{$this->host}:{$this->port}";
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getEmbeddingFunction(): ?EmbeddingFunction
    {
        return $this->embeddingFunction;
    }

    public function withTenant(string $tenant): self
    {
        $clone = clone $this;
        $clone->tenant = $tenant;

        return $clone;
    }

    public function withDatabase(string $database): self
    {
        $clone = clone $this;
        $clone->database = $database;

        return $clone;
    }

    public function withEmbeddings(EmbeddingFunction $embeddingFunction): self
    {
        $clone = clone $this;
        $clone->embeddingFunction = $embeddingFunction;

        return $clone;
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
