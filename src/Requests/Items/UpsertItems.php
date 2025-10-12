<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * upsert
 */
class UpsertItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/upsert";
    }

    public function __construct(
        protected string $collectionId,
        protected array $ids,
        protected null|array|string $embeddings = null,
        protected null|array|string $metadatas = null,
        protected null|array|string $documents = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    protected function defaultBody(): array
    {
        return array_filter([
            'ids' => $this->ids,
            'embeddings' => $this->embeddings,
            'metadatas' => $this->metadatas,
            'documents' => $this->documents,
        ]);
    }
}
