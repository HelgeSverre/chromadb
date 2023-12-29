<?php

namespace HelgeSverre\Chromadb\Requests\Others;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * upsert
 */
class Upsert extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/upsert";
    }

    public function __construct(
        protected string $collectionId,
    ) {
    }
}
