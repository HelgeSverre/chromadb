<?php

namespace HelgeSverre\Chromadb\Requests\Database;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create_database
 */
class CreateDatabase extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';

        return "/api/v2/tenants/{$tenant}/databases";
    }

    public function __construct(
        protected string $name,
        protected ?string $tenant = null,
    ) {}

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
