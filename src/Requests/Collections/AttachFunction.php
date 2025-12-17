<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Attaches a function to a collection for server-side processing.
 *
 * Attached functions enable automatic processing of collection data,
 * such as computing statistics or triggering workflows when records are added.
 *
 * Note: This endpoint is only available in ChromaDB 1.3.0+
 */
class AttachFunction extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $collectionId,
        protected string $name,
        protected string $functionId,
        protected string $outputCollection,
        protected ?array $params = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/functions/attach";
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'function_id' => $this->functionId,
            'output_collection' => $this->outputCollection,
            'params' => $this->params,
        ], fn ($value) => $value !== null);
    }
}
