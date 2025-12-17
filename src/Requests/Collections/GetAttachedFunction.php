<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Gets details of an attached function by name.
 *
 * Returns information about an attached function including its configuration,
 * input/output collections, and processing status.
 *
 * Note: This endpoint is only available in ChromaDB 1.3.0+
 */
class GetAttachedFunction extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $collectionId,
        protected string $functionName,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/functions/{$this->functionName}";
    }
}
