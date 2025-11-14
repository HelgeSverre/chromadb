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
    /**
     * Add items to a collection with embeddings, metadata, and documents.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  array|null  $ids  Array of unique IDs for the items (auto-generated if null)
     * @param  array|string|null  $embeddings  Array of embedding vectors (2D array) or single embedding
     * @param  array|null  $metadatas  Array of metadata objects (one per item)
     * @param  array|string|null  $documents  Array of document strings or single document
     * @param  array|null  $uris  Array of URIs referencing external documents
     * @return Response Response confirming items were added
     */
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

    /**
     * Update existing items in a collection.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  array  $ids  Array of IDs for items to update (must exist)
     * @param  array|string|null  $embeddings  New embedding vectors
     * @param  array|null  $metadatas  New metadata (replaces existing)
     * @param  array|string|null  $documents  New document content
     * @param  array|null  $uris  New URIs
     * @return Response Response confirming items were updated
     */
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

    /**
     * Upsert items (insert if not exist, update if exist) in a collection.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  array  $ids  Array of IDs (creates new items or updates existing)
     * @param  array|string|null  $embeddings  Embedding vectors
     * @param  array|null  $metadatas  Metadata objects
     * @param  array|string|null  $documents  Document content
     * @param  array|null  $uris  URIs
     * @return Response Response confirming upsert operation
     */
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

    /**
     * Retrieve specific items from a collection by IDs or filters.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  string|array  $ids  Single ID or array of IDs to retrieve
     * @param  array|null  $include  What to include in response: ['documents', 'metadatas', 'embeddings', 'distances', 'uris']
     * @param  int|null  $limit  Maximum number of items to return (pagination)
     * @param  int|null  $offset  Number of items to skip (pagination)
     * @param  array|null  $where  Metadata filters (e.g., ['category' => 'tech', 'year' => ['$gte' => 2020]])
     * @param  array|null  $whereDocument  Document content filters (e.g., ['$contains' => 'keyword'])
     * @return Response Response containing matching items
     */
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

    /**
     * Delete items from a collection by IDs or filters.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  array|null  $ids  Array of IDs to delete (optional if using filters)
     * @param  array|null  $where  Metadata filters to match items for deletion
     * @param  array|null  $whereDocument  Document content filters to match items for deletion
     * @return Response Response confirming deletion
     */
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

    /**
     * Count the total number of items in a collection.
     *
     * @param  string  $collectionId  UUID of the collection
     * @return int Total number of items in the collection
     */
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

    /**
     * Query items in a collection by semantic similarity using embeddings or text.
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  array  $queryEmbeddings  Array of query embedding vectors for similarity search
     * @param  array|null  $queryTexts  Array of query texts (requires embedding function configured)
     * @param  array|null  $where  Metadata filters to apply before similarity search
     * @param  array|null  $whereDocument  Document content filters to apply before similarity search
     * @param  array|null  $include  What to include in response: ['documents', 'metadatas', 'embeddings', 'distances', 'uris']
     * @param  int|null  $nResults  Number of nearest neighbors to return per query
     * @param  int|null  $limit  Maximum total results (pagination)
     * @param  int|null  $offset  Number of results to skip (pagination)
     * @return Response Response containing query results with similarity scores
     */
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
