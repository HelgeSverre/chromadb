<?php

namespace HelgeSverre\Chromadb\Requests\Items;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * get_nearest_neighbors
 */
class QueryItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/api/v1/collections/{$this->collectionId}/query";
    }

    public function __construct(
        protected string $collectionId,
        protected array $queryEmbeddings = [],
        protected ?array $queryTexts = null,
        protected ?array $where = null,
        protected ?array $whereDocument = null,
        protected ?array $include = null,
        protected ?int $nResults = null,
    ) {
    }

    protected function defaultBody(): array
    {
        return [
            'where' => $this->where,
            'where_document' => $this->whereDocument,
            'query_embeddings' => $this->queryEmbeddings,
            'query_texts' => $this->queryTexts,
            'n_results' => $this->nResults,
            'include' => $this->include,
        ];
    }
}
