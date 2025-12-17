<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Detaches a function from a collection.
 *
 * This stops the attached function from processing new records.
 * Optionally deletes the output collection that was created by the function.
 *
 * Note: This endpoint is only available in ChromaDB 1.3.0+
 */
class DetachFunction extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $collectionId,
        protected string $functionName,
        protected bool $deleteOutput = false,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/attached_functions/{$this->functionName}/detach";
    }

    protected function defaultBody(): array
    {
        return [
            'delete_output' => $this->deleteOutput,
        ];
    }
}
