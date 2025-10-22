<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Retrieves a collection by Chroma Resource Name (CRN) format.
 */
class GetCollectionByCrn extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $crn,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/collections/{$this->crn}";
    }
}
