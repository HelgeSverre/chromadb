<?php

namespace HelgeSverre\Chromadb\Requests\Tenant;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * get_tenant
 */
class GetTenant extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/api/v2/tenants/{$this->tenant}";
    }

    public function __construct(
        protected string $tenant,
    ) {}
}
