<?php

namespace HelgeSverre\Chromadb\Requests\Tenant;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create_tenant
 */
class CreateTenant extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/api/v1/tenants';
    }

    public function __construct(protected string $name)
    {
    }

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
