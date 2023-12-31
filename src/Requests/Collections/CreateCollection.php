<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create_collection
 */
class CreateCollection extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/api/v1/collections';
    }

    public function __construct(
        protected string $name,
        protected bool $getOrCreate = false,
        protected ?array $metadata = null,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {
    }

    public function defaultQuery(): array
    {
        return array_filter([
            'tenant' => $this->tenant,
            'database' => $this->database,
        ]);
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'name' => $this->name,
            'get_or_create' => $this->getOrCreate,
            'metadata' => $this->metadata,
        ]);
    }
}
