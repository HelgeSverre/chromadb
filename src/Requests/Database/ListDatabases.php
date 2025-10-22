<?php

namespace HelgeSverre\Chromadb\Requests\Database;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Lists all databases for a given tenant.
 *
 * Supports pagination via limit and offset query parameters.
 */
class ListDatabases extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ?int $limit = null,
        protected ?int $offset = null,
        protected ?string $tenant = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';

        return "/api/v2/tenants/{$tenant}/databases";
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'limit' => $this->limit,
            'offset' => $this->offset,
        ], fn ($value) => $value !== null);
    }
}
