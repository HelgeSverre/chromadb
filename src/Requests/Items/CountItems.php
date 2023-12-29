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
        return "/api/v1/collections/{$this->collectionId}/count";
    }

    public function __construct(
        protected string $collectionId,
    ) {
    }
}
