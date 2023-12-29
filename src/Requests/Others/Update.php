<?php

namespace HelgeSverre\Chromadb\Requests\Others;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * update
 */
class Update extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/update";
    }

    public function __construct(
        protected string $collectionId,
    ) {
    }
}
