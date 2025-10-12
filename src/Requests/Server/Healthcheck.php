<?php

namespace HelgeSverre\Chromadb\Requests\Server;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Health check endpoint that returns 200 if the server and executor are ready.
 *
 * This endpoint verifies the entire system including executor readiness,
 * unlike heartbeat which only checks if the server is responsive.
 *
 * Returns 200 if healthy, 503 if service unavailable.
 */
class Healthcheck extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/v2/healthcheck';
    }
}
