<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * count_collections
 */
class CountCollections extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v1/count_collections';
    }

    public function __construct(
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter(['tenant' => $this->tenant, 'database' => $this->database]);
    }
}
