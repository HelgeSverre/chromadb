<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * update_collection
 */
class UpdateCollection extends Request
{
    protected Method $method = Method::PUT;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}";
    }

    public function __construct(
        protected string $collectionId,
        protected ?string $newName,

        // TODO: Does not seem to actually work, investigate if the docs lie
        protected ?array $newMetadata,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter([
            'new_name' => $this->newName,
            'new_metadata' => $this->newMetadata,
        ]);
    }
}
