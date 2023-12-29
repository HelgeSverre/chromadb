<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * list_collections
 */
class ListCollections extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v1/collections';
    }

    public function __construct(
        protected ?int $limit = null,
        protected ?int $offset = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter([
            'limit' => $this->limit,
            'offset' => $this->offset,
            'tenant' => $this->tenant,
            'database' => $this->database,
        ]);
    }
}
