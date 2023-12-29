<?php

namespace HelgeSverre\Chromadb\Requests\Server;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Gets the version of the ChromaDB server.
 */
class Version extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v1/version';
    }
}
