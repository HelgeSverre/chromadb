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
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections";
    }

    public function __construct(
        protected ?int $limit = null,
        protected ?int $offset = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter([
            'limit' => $this->limit,
            'offset' => $this->offset,
        ]);
    }
}
