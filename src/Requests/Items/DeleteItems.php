<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * delete
 */
class DeleteItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/delete";
    }

    public function __construct(
        protected string $collectionId,
        protected ?array $ids = null,
        protected ?array $where = null,
        protected ?array $whereDocument = null,
    ) {
    }

    protected function defaultBody(): array
    {
        return [
            'ids' => $this->ids,
            'where' => $this->where,
            'whereDocument' => $this->whereDocument,
        ];
    }
}
