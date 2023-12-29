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
        return "/api/v1/collections/{$this->collectionId}";
    }

    public function __construct(
        protected string $collectionId,
        protected ?string $newName,

        // TODO: Does not seem to actually work, investigate if the docs lie
        protected ?array $newMetadata,
    ) {
    }

    public function defaultBody(): array
    {
        return array_filter([
            'new_name' => $this->newName,
            'new_metadata' => $this->newMetadata,
        ]);
    }
}
