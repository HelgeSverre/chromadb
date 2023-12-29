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
        return '/api/v1/databases';
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

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
