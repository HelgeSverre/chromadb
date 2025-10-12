<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * get_collection
 */
class GetCollection extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionName}";
    }

    public function __construct(
        protected string $collectionName,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}
}
