<?php

namespace HelgeSverre\Chromadb\Resource;

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
        ?array $embeddings = null,
        ?array $metadatas = null,
        ?array $documents = null
    ): Response
    {
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
        ?array $embeddings = null,
        ?array $metadatas = null,
        ?array $documents = null
    ): Response
    {
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
        ?array $embeddings = null,
        ?array $metadatas = null,
        ?array $documents = null
    ): Response
    {
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
    ): Response
    {
        return $this->connector->send(new GetItems(
            collectionId: $collectionId
        ));
    }

    public function delete(
        string $collectionId,
        array $ids = [],
        array $where = [],
        array $whereDocument = []
    ): Response
    {
        return $this->connector->send(new DeleteItems(
            collectionId: $collectionId,
            ids: $ids,
            where: $where,
            whereDocument: $whereDocument
        ));
    }

    public function count(
        string $collectionId
    ): Response
    {
        return $this->connector->send(new CountItems(
            collectionId: $collectionId
        ));
    }

    public function query(
        string $collectionId,
        array $queryEmbeddings,
        array $where = [],
        array $whereDocument = [],
        array $include = ['embeddings', 'documents', 'metadatas'],
        int $nResults = 10
    ): Response
    {
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
