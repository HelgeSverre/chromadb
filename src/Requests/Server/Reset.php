<?php

namespace HelgeSverre\Chromadb\Requests\Server;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * reset
 */
class Reset extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/api/v2/reset';
    }

    public function __construct() {}
}
