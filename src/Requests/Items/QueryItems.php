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
        protected array $queryEmbeddings,
        protected array $where = [],
        protected array $whereDocument = [],

        // can by any of : "distances", "embeddings", "documents", "metadatas"
        protected array $include = ['embeddings', 'documents', 'metadatas'],
        protected int $nResults = 10,
    ) {
    }

    protected function defaultBody(): array
    {
        return array_filter([
            'where' => $this->where,
            'where_document' => $this->whereDocument,
            'query_embeddings' => $this->queryEmbeddings,
            'n_results' => $this->nResults,
            'include' => $this->include,
        ]);
    }
}
