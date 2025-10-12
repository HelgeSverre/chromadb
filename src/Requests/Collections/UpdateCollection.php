<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * update_collection
 */
class UpdateCollection extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}";
    }

    public function __construct(
        protected string $collectionId,
        protected ?string $newName,
        protected ?array $newMetadata,
        protected ?array $newConfiguration = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function defaultBody(): array
    {
        return array_filter([
            'new_name' => $this->newName,
            'new_metadata' => $this->newMetadata,
            'new_configuration' => $this->newConfiguration,
        ], fn ($value) => $value !== null);
    }
}
