<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * add
 */
class AddItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/add";
    }

    public function __construct(
        protected string $collectionId,

        // TODO: Make this a DTO
        protected null|array|string $ids = null,
        protected null|array|string $embeddings = null,
        protected null|array|string $metadatas = null,
        protected null|array|string $documents = null,
    ) {
    }

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
