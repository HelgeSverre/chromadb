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
use HelgeSverre\Chromadb\Requests\Others\Add;
use HelgeSverre\Chromadb\Requests\Others\Count;
use HelgeSverre\Chromadb\Requests\Others\Delete;
use HelgeSverre\Chromadb\Requests\Others\Get;
use HelgeSverre\Chromadb\Requests\Others\GetNearestNeighbors;
use HelgeSverre\Chromadb\Requests\Others\Update;
use HelgeSverre\Chromadb\Requests\Others\Upsert;
use HelgeSverre\Chromadb\Requests\Server\Heartbeat;
use HelgeSverre\Chromadb\Requests\Server\HeartBeat;
use HelgeSverre\Chromadb\Requests\Server\PreFlightChecks;
use HelgeSverre\Chromadb\Requests\Server\Reset;
use HelgeSverre\Chromadb\Requests\Server\Version;
use HelgeSverre\Chromadb\Requests\Tenant\CreateTenant;
use HelgeSverre\Chromadb\Requests\Tenant\GetTenant;
use HelgeSverre\Chromadb\Resource;
use Saloon\Http\Response;

class Misc extends Resource
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
        return $this->connector->send(new Add($collectionId));
    }

    public function update(string $collectionId): Response
    {
        return $this->connector->send(new Update($collectionId));
    }

    public function upsert(string $collectionId): Response
    {
        return $this->connector->send(new Upsert($collectionId));
    }

    public function get(string $collectionId): Response
    {
        return $this->connector->send(new Get($collectionId));
    }

    public function delete(string $collectionId): Response
    {
        return $this->connector->send(new Delete($collectionId));
    }

    public function count(string $collectionId): Response
    {
        return $this->connector->send(new Count($collectionId));
    }

    public function getNearestNeighbors(string $collectionId): Response
    {
        return $this->connector->send(new GetNearestNeighbors($collectionId));
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
