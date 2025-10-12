<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * count
 */
class CountItems extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/count";
    }

    public function __construct(
        protected string $collectionId,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}
}
