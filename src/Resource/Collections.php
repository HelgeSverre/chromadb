<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Collections\CountCollections;
use HelgeSverre\Chromadb\Requests\Collections\CreateCollection;
use HelgeSverre\Chromadb\Requests\Collections\DeleteCollection;
use HelgeSverre\Chromadb\Requests\Collections\GetCollection;
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
            tenant: $tenant,
            database: $database,
        ));
    }

    public function create(
        string $name,
        bool $getOrCreate = false,
        ?array $metadata = null,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new CreateCollection(
            name: $name,
            getOrCreate: $getOrCreate,
            metadata: $metadata,
            tenant: $tenant,
            database: $database,
        ));
    }

    public function count(
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new CountCollections(
            tenant: $tenant,
            database: $database
        ));
    }

    public function get(
        string $collectionName,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new GetCollection(
            collectionName: $collectionName,
            tenant: $tenant,
            database: $database
        ));
    }

    public function delete(
        string $collectionName,
        ?string $tenant = null,
        ?string $database = null,
    ): Response {
        return $this->connector->send(new DeleteCollection(
            collectionName: $collectionName,
            tenant: $tenant,
            database: $database

        ));
    }

    public function update(
        string $collectionId,
        ?string $newName = null,
        ?array $newMetadata = null,
    ): Response {
        return $this->connector->send(new UpdateCollection(
            collectionId: $collectionId,
            newName: $newName,
            newMetadata: $newMetadata,
        ));
    }
}
