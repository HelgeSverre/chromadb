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
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections_count";
    }

    public function __construct(
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}
}
