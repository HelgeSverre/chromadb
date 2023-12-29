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
    public function listCollections(mixed $limit, mixed $offset, ?string $tenant, ?string $database): Response
    {
        return $this->connector->send(new ListCollections($limit, $offset, $tenant, $database));
    }

    public function createCollection(?string $tenant, ?string $database): Response
    {
        return $this->connector->send(new CreateCollection($tenant, $database));
    }

    public function countCollections(?string $tenant, ?string $database): Response
    {
        return $this->connector->send(new CountCollections($tenant, $database));
    }

    public function getCollection(string $collectionName, ?string $tenant, ?string $database): Response
    {
        return $this->connector->send(new GetCollection($collectionName, $tenant, $database));
    }

    public function deleteCollection(string $collectionName, ?string $tenant, ?string $database): Response
    {
        return $this->connector->send(new DeleteCollection($collectionName, $tenant, $database));
    }

    public function updateCollection(string $collectionId): Response
    {
        return $this->connector->send(new UpdateCollection($collectionId));
    }
}
