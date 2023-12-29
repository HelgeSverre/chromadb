<?php

namespace HelgeSverre\Chromadb\Requests\Database;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * get_database
 */
class GetDatabase extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/api/v1/databases/{$this->database}";
    }

    public function __construct(
        protected string $database,
        protected ?string $tenant,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter(['tenant' => $this->tenant]);
    }
}
