<?php

namespace HelgeSverre\Chromadb\Requests\Tenant;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Updates a tenant's resource_name.
 */
class UpdateTenant extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $tenantName,
        protected string $resourceName,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/tenants/{$this->tenantName}";
    }

    protected function defaultBody(): array
    {
        return ['resource_name' => $this->resourceName];
    }
}
