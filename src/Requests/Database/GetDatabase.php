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
        return "/api/v1/databases/{$this->name}";
    }

    public function __construct(
        protected string $name,
        protected ?string $tenant = null,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter([
            'tenant' => $this->tenant,
        ]);
    }
}
