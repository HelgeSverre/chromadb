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
        $tenant = $this->tenant ?? 'default_tenant';

        return "/api/v2/tenants/{$tenant}/databases/{$this->name}";
    }

    public function __construct(
        protected string $name,
        protected ?string $tenant = null,
    ) {}
}
