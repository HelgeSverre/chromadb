<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * get
 */
class GetItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/get";
    }

    public function __construct(
        protected string $collectionId,
        protected string|array $ids,
        protected ?array $include = null,
        protected ?int $limit = null,
        protected ?int $offset = null,
        protected ?array $where = null,
        protected ?array $whereDocument = null,
    ) {
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'ids' => $this->ids,
            'include' => $this->include,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'where' => $this->where,
            'whereDocument' => $this->whereDocument,
        ]);
    }
}
