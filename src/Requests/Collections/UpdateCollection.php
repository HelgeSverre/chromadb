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
    ) {
    }
}
