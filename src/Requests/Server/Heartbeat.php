<?php

namespace HelgeSverre\Chromadb\Requests\Server;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get the status of the ChromaDB server.
 *
 * Example:
 * {
 *  "nanosecond heartbeat": 1703831284585978374
 * }
 */
class Heartbeat extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v1/heartbeat';
    }
}
