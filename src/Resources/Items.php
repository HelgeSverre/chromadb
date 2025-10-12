<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;
use HelgeSverre\Chromadb\Requests\Items\AddItems;
use HelgeSverre\Chromadb\Requests\Items\CountItems;
use HelgeSverre\Chromadb\Requests\Items\DeleteItems;
use HelgeSverre\Chromadb\Requests\Items\GetItems;
use HelgeSverre\Chromadb\Requests\Items\QueryItems;
use HelgeSverre\Chromadb\Requests\Items\SearchItems;
use HelgeSverre\Chromadb\Requests\Items\UpdateItems;
use HelgeSverre\Chromadb\Requests\Items\UpsertItems;
use RuntimeException;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Items extends BaseResource
{
    public function add(
        string $collectionId,
        ?array $ids = null,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null,
        ?array $uris = null
    ): Response {
        return $this->connector->send(new AddItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents,
            uris: $uris,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }

    public function update(
        string $collectionId,
        array $ids,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null,
        ?array $uris = null
    ): Response {
        return $this->connector->send(new UpdateItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents,
            uris: $uris,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }

    public function upsert(
        string $collectionId,
        array $ids,
        null|array|string $embeddings = null,
        ?array $metadatas = null,
        null|array|string $documents = null,
        ?array $uris = null
    ): Response {
        return $this->connector->send(new UpsertItems(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents,
            uris: $uris,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }

    public function get(
        string $collectionId,
        string|array $ids,
        ?array $include = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $where = null,
        ?array $whereDocument = null,
    ): Response {
        return $this->connector->send(new GetItems(
            collectionId: $collectionId,
            ids: $ids,
            include: $include,
            limit: $limit,
            offset: $offset,
            where: $where,
            whereDocument: $whereDocument,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
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
            whereDocument: $whereDocument,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }

    public function count(
        string $collectionId
    ): int {
        $response = $this->connector->send(new CountItems(
            collectionId: $collectionId,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));

        // The response from this endpoint is not JSON, its just plain text.
        return (int) $response->body();
    }

    public function query(
        string $collectionId,
        array $queryEmbeddings = [],
        ?array $queryTexts = null,
        ?array $where = null,
        ?array $whereDocument = null,
        ?array $include = null,
        ?int $nResults = null,
        ?int $limit = null,
        ?int $offset = null,
    ): Response {
        return $this->connector->send(new QueryItems(
            collectionId: $collectionId,
            queryEmbeddings: $queryEmbeddings,
            queryTexts: $queryTexts,
            where: $where,
            whereDocument: $whereDocument,
            include: $include,
            nResults: $nResults,
            limit: $limit,
            offset: $offset,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }

    /**
     * Add items to a collection with automatic embedding generation.
     *
     * This is a convenience method that automatically generates embeddings
     * from documents using the provided or configured embedding function.
     *
     * @param  string  $collectionId  The collection ID
     * @param  array<string>  $documents  The documents to add
     * @param  EmbeddingFunction|null  $embeddingFunction  The embedding function to use (uses configured default if null)
     * @param  array<string>|null  $ids  Optional IDs (auto-generated if not provided)
     * @param  array|null  $metadatas  Optional metadata for each document
     *
     * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
     */
    public function addWithEmbeddings(
        string $collectionId,
        array $documents,
        ?EmbeddingFunction $embeddingFunction = null,
        ?array $ids = null,
        ?array $metadatas = null
    ): Response {
        // Use provided embedding function or fall back to configured default
        $function = $embeddingFunction ?? $this->connector->getEmbeddingFunction();

        if ($function === null) {
            throw new RuntimeException(
                'No embedding function configured. Either pass one explicitly or configure a default in config/chromadb.php'
            );
        }

        // Generate embeddings from documents
        $embeddings = $function->generate($documents);

        // Auto-generate IDs if not provided
        if ($ids === null) {
            $ids = array_map(fn ($i) => uniqid("item_{$i}_", true), array_keys($documents));
        }

        return $this->add(
            collectionId: $collectionId,
            ids: $ids,
            embeddings: $embeddings,
            metadatas: $metadatas,
            documents: $documents
        );
    }

    /**
     * Query a collection using a text query with automatic embedding generation.
     *
     * This is a convenience method that automatically generates a query embedding
     * from text using the provided or configured embedding function.
     *
     * @param  string  $collectionId  The collection ID
     * @param  string  $queryText  The text query
     * @param  EmbeddingFunction|null  $embeddingFunction  The embedding function to use (uses configured default if null)
     * @param  int|null  $nResults  Number of results to return (default: 10)
     * @param  array|null  $include  Fields to include in results
     * @param  array|null  $where  Metadata filter
     * @param  array|null  $whereDocument  Document filter
     *
     * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
     */
    public function queryWithText(
        string $collectionId,
        string $queryText,
        ?EmbeddingFunction $embeddingFunction = null,
        ?int $nResults = 10,
        ?array $include = null,
        ?array $where = null,
        ?array $whereDocument = null
    ): Response {
        // Use provided embedding function or fall back to configured default
        $function = $embeddingFunction ?? $this->connector->getEmbeddingFunction();

        if ($function === null) {
            throw new RuntimeException(
                'No embedding function configured. Either pass one explicitly or configure a default in config/chromadb.php'
            );
        }

        // Generate embedding from query text
        $queryEmbeddings = $function->generate([$queryText]);

        return $this->query(
            collectionId: $collectionId,
            queryEmbeddings: $queryEmbeddings,
            nResults: $nResults,
            include: $include,
            where: $where,
            whereDocument: $whereDocument
        );
    }

    public function search(string $collectionId, array $searches): Response
    {
        return $this->connector->send(new SearchItems(
            collectionId: $collectionId,
            searches: $searches,
            tenant: $this->connector->getTenant(),
            database: $this->connector->getDatabase()
        ));
    }
}
