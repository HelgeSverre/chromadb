<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Search records from a collection with hybrid search criteria.
 *
 * The search endpoint supports hybrid search combining multiple search strategies
 * including vector similarity, metadata filtering, and field selection.
 */
class SearchItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $collectionId,
        protected array $searches,
        protected ?string $tenant = null,
        protected ?string $database = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';
        $database = $this->database ?? 'default_database';

        return "/api/v2/tenants/{$tenant}/databases/{$database}/collections/{$this->collectionId}/search";
    }

    protected function defaultBody(): array
    {
        return ['searches' => $this->searches];
    }
}
