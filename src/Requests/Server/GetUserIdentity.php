<?php

namespace HelgeSverre\Chromadb\Requests\Server;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Retrieves the current user's identity, tenant, and available databases.
 */
class GetUserIdentity extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v2/auth/identity';
    }
}
