<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Collections\CountCollections;
use HelgeSverre\Chromadb\Requests\Collections\CreateCollection;
use HelgeSverre\Chromadb\Requests\Collections\DeleteCollection;
use HelgeSverre\Chromadb\Requests\Collections\ForkCollection;
use HelgeSverre\Chromadb\Requests\Collections\GetCollection;
use HelgeSverre\Chromadb\Requests\Collections\GetCollectionByCrn;
use HelgeSverre\Chromadb\Requests\Collections\ListCollections;
use HelgeSverre\Chromadb\Requests\Collections\UpdateCollection;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Collections extends BaseResource
{
    /**
     * List all collections in the specified tenant and database.
     *
     * @param  int|null  $limit  Maximum number of collections to return (pagination)
     * @param  int|null  $offset  Number of collections to skip (pagination)
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing array of collection objects
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new ListCollections(
            limit: $limit,
            offset: $offset,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }

    /**
     * Create a new collection with optional metadata and configuration.
     *
     * @param  string  $name  Name of the collection to create
     * @param  bool  $getOrCreate  If true, return existing collection if name already exists
     * @param  array|null  $metadata  Optional metadata key-value pairs
     * @param  array|null  $configuration  Optional HNSW configuration (e.g., ['hnsw:space' => 'cosine', 'hnsw:construction_ef' => 100, 'hnsw:M' => 16])
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing the created collection details
     */
    public function create(
        string $name,
        bool $getOrCreate = false,
        ?array $metadata = null,
        ?array $configuration = null,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new CreateCollection(
            name: $name,
            getOrCreate: $getOrCreate,
            metadata: $metadata,
            configuration: $configuration,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }

    /**
     * Count the total number of collections in the specified tenant and database.
     *
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return int Total number of collections
     */
    public function count(
        ?string $tenant = null,
        ?string $database = null,
    ): int {
        $response = $this->connector->send(new CountCollections(
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase()
        ));

        // The response from this endpoint is not JSON, its just plain text.
        return (int) $response->body();

    }

    /**
     * Retrieve a collection by its name.
     *
     * @param  string  $collectionName  Name of the collection to retrieve
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing collection details (id, name, metadata, etc.)
     */
    public function get(
        string $collectionName,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new GetCollection(
            collectionName: $collectionName,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase()
        ));
    }

    /**
     * Delete a collection by its name.
     *
     * @param  string  $collectionName  Name of the collection to delete
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response confirming deletion
     */
    public function delete(
        string $collectionName,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new DeleteCollection(
            collectionName: $collectionName,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase()

        ));
    }

    /**
     * Update a collection's name, metadata, or configuration.
     *
     * @param  string  $collectionId  UUID of the collection to update
     * @param  string|null  $newName  New name for the collection
     * @param  array|null  $newMetadata  New metadata to replace existing metadata
     * @param  array|null  $newConfiguration  New HNSW configuration
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing updated collection details
     */
    public function update(
        string $collectionId,
        ?string $newName = null,
        ?array $newMetadata = null,
        ?array $newConfiguration = null,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new UpdateCollection(
            collectionId: $collectionId,
            newName: $newName,
            newMetadata: $newMetadata,
            newConfiguration: $newConfiguration,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }

    /**
     * Create a copy (fork) of an existing collection.
     *
     * Note: Fork endpoint is only available in ChromaDB Cloud, not in local installations.
     *
     * @param  string  $collectionId  UUID of the collection to fork
     * @param  string  $newName  Name for the new forked collection
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing the new collection details
     */
    public function fork(string $collectionId, string $newName, ?string $tenant = null, ?string $database = null): Response
    {
        return $this->connector->send(new ForkCollection(
            collectionId: $collectionId,
            newName: $newName,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase()
        ));
    }

    /**
     * Retrieve a collection by its CRN (Collection Resource Name).
     *
     * CRN format: crn:chroma:collection:tenant_name:database_name:collection_id
     * Note: CRN endpoint implementation varies between ChromaDB versions.
     *
     * @param  string  $crn  Full Collection Resource Name
     * @return Response Response containing collection details
     */
    public function getByCrn(string $crn): Response
    {
        return $this->connector->send(new GetCollectionByCrn(crn: $crn));
    }
}
