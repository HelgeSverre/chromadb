<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Collections\CountCollections;
use HelgeSverre\Chromadb\Requests\Collections\CreateCollection;
use HelgeSverre\Chromadb\Requests\Collections\DeleteCollection;
use HelgeSverre\Chromadb\Requests\Collections\GetCollection;
use HelgeSverre\Chromadb\Requests\Collections\ListCollections;
use HelgeSverre\Chromadb\Requests\Collections\UpdateCollection;
use HelgeSverre\Chromadb\Requests\Database\CreateDatabase;
use HelgeSverre\Chromadb\Requests\Database\GetDatabase;
use HelgeSverre\Chromadb\Requests\Items\AddItems;
use HelgeSverre\Chromadb\Requests\Items\CountItems;
use HelgeSverre\Chromadb\Requests\Items\DeleteItems;
use HelgeSverre\Chromadb\Requests\Items\GetItems;
use HelgeSverre\Chromadb\Requests\Items\QueryItems;
use HelgeSverre\Chromadb\Requests\Items\UpdateItems;
use HelgeSverre\Chromadb\Requests\Items\UpsertItems;
use HelgeSverre\Chromadb\Requests\Server\Heartbeat;
use HelgeSverre\Chromadb\Requests\Server\PreFlightChecks;
use HelgeSverre\Chromadb\Requests\Server\Reset;
use HelgeSverre\Chromadb\Requests\Server\Version;
use HelgeSverre\Chromadb\Requests\Tenant\CreateTenant;
use HelgeSverre\Chromadb\Requests\Tenant\GetTenant;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Server extends BaseResource
{
    public function root(): Response
    {
        return $this->connector->send(new HeartBeat());
    }

    public function reset(): Response
    {
        return $this->connector->send(new Reset());
    }

    public function version(): Response
    {
        return $this->connector->send(new Version());
    }

    public function heartbeat(): Response
    {
        return $this->connector->send(new Heartbeat());
    }

    public function preFlightChecks(): Response
    {
        return $this->connector->send(new PreFlightChecks());
    }

    public function createDatabase(?string $tenant): Response
    {
        return $this->connector->send(new CreateDatabase($tenant));
    }

    public function getDatabase(string $database, ?string $tenant): Response
    {
        return $this->connector->send(new GetDatabase($database, $tenant));
    }

    public function createTenant(): Response
    {
        return $this->connector->send(new CreateTenant());
    }

    public function getTenant(string $tenant): Response
    {
        return $this->connector->send(new GetTenant($tenant));
    }

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

    public function add(string $collectionId): Response
    {
        return $this->connector->send(new AddItems($collectionId));
    }

    public function update(string $collectionId): Response
    {
        return $this->connector->send(new UpdateItems($collectionId));
    }

    public function upsert(string $collectionId): Response
    {
        return $this->connector->send(new UpsertItems($collectionId));
    }

    public function get(string $collectionId): Response
    {
        return $this->connector->send(new GetItems($collectionId));
    }

    public function delete(string $collectionId): Response
    {
        return $this->connector->send(new DeleteItems($collectionId));
    }

    public function count(string $collectionId): Response
    {
        return $this->connector->send(new CountItems($collectionId));
    }

    public function getNearestNeighbors(string $collectionId): Response
    {
        return $this->connector->send(new QueryItems($collectionId));
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
