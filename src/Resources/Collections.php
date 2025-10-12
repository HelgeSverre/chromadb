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

    public function fork(string $collectionId, string $newName, ?string $tenant = null, ?string $database = null): Response
    {
        return $this->connector->send(new ForkCollection(
            collectionId: $collectionId,
            newName: $newName,
            tenant: $tenant ?? $this->connector->getTenant(),
            database: $database ?? $this->connector->getDatabase()
        ));
    }

    public function getByCrn(string $crn): Response
    {
        return $this->connector->send(new GetCollectionByCrn(crn: $crn));
    }
}
