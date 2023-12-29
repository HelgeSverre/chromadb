<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Items\AddItems;
use HelgeSverre\Chromadb\Requests\Items\CountItems;
use HelgeSverre\Chromadb\Requests\Items\DeleteItems;
use HelgeSverre\Chromadb\Requests\Items\GetItems;
use HelgeSverre\Chromadb\Requests\Items\QueryItems;
use HelgeSverre\Chromadb\Requests\Items\UpdateItems;
use HelgeSverre\Chromadb\Requests\Items\UpsertItems;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Items extends BaseResource
{
    public function add(
        string $collectionId,
        ?array $ids = null,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null
    ): Response {
        return $this->connector->send(new AddItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents
        ));
    }

    public function update(
        string $collectionId,
        array $ids,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null
    ): Response {
        return $this->connector->send(new UpdateItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents
        ));
    }

    public function upsert(
        string $collectionId,
        array $ids,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null
    ): Response {
        return $this->connector->send(new UpsertItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents
        ));
    }

    public function get(
        string $collectionId
    ): Response {
        return $this->connector->send(new GetItems(
            collectionId: $collectionId
        ));
    }

    public function delete(
        string $collectionId,
        ?array $ids = null,
        ?array $where = null,
        ?array $whereDocument = null
    ): Response {
        return $this->connector->send(new DeleteItems(
            collectionId: $collectionId,
            ids: $ids,
            where: $where,
            whereDocument: $whereDocument
        ));
    }

    public function count(
        string $collectionId
    ): int {
        $response = $this->connector->send(new CountItems(
            collectionId: $collectionId
        ));

        // The response from this endpoint is not JSON, its just plain text.
        return (int) $response->body();
    }

    public function query(
        string $collectionId,
        array $queryEmbeddings,
        array $where = [],
        array $whereDocument = [],
        array $include = ['embeddings', 'documents', 'metadatas'],
        int $nResults = 10
    ): Response {
        return $this->connector->send(new QueryItems(
            collectionId: $collectionId,
            queryEmbeddings: $queryEmbeddings,
            where: $where,
            whereDocument: $whereDocument,
            include: $include,
            nResults: $nResults
        ));
    }
}
