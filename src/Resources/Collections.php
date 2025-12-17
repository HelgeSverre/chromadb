<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Collections\AttachFunction;
use HelgeSverre\Chromadb\Requests\Collections\CountCollections;
use HelgeSverre\Chromadb\Requests\Collections\CreateCollection;
use HelgeSverre\Chromadb\Requests\Collections\DeleteCollection;
use HelgeSverre\Chromadb\Requests\Collections\DetachFunction;
use HelgeSverre\Chromadb\Requests\Collections\ForkCollection;
use HelgeSverre\Chromadb\Requests\Collections\GetAttachedFunction;
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

    /**
     * Attach a function to a collection for server-side processing.
     *
     * Attached functions enable automatic processing of collection data,
     * such as computing statistics or triggering workflows when records are added.
     *
     * Note: This endpoint is only available in ChromaDB 1.3.0+
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  string  $name  Human-readable name for this attached function instance
     * @param  string  $functionId  ID of the function to attach (e.g., 'statistics', 'record_counter')
     * @param  string  $outputCollection  Name of the collection to store function output
     * @param  array|null  $params  Optional JSON parameters for the function
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing attached function details and whether it was newly created
     */
    public function attachFunction(
        string $collectionId,
        string $name,
        string $functionId,
        string $outputCollection,
        ?array $params = null,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new AttachFunction(
            collectionId: $collectionId,
            name: $name,
            functionId: $functionId,
            outputCollection: $outputCollection,
            params: $params,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }

    /**
     * Get details of an attached function by name.
     *
     * Returns information about an attached function including its configuration,
     * input/output collections, completion offset, and processing status.
     *
     * Note: This endpoint is only available in ChromaDB 1.3.0+
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  string  $functionName  Name of the attached function
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response containing attached function details
     */
    public function getAttachedFunction(
        string $collectionId,
        string $functionName,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new GetAttachedFunction(
            collectionId: $collectionId,
            functionName: $functionName,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }

    /**
     * Detach a function from a collection.
     *
     * This stops the attached function from processing new records.
     * Optionally deletes the output collection that was created by the function.
     *
     * Note: This endpoint is only available in ChromaDB 1.3.0+
     *
     * @param  string  $collectionId  UUID of the collection
     * @param  string  $functionName  Name of the attached function to detach
     * @param  bool  $deleteOutput  Whether to delete the output collection as well
     * @param  string|null  $tenant  Override default tenant
     * @param  string|null  $database  Override default database
     * @return Response Response confirming detachment
     */
    public function detachFunction(
        string $collectionId,
        string $functionName,
        bool $deleteOutput = false,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new DetachFunction(
            collectionId: $collectionId,
            functionName: $functionName,
            deleteOutput: $deleteOutput,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase(),
        ));
    }
}
