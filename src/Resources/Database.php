<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Database\CreateDatabase;
use HelgeSverre\Chromadb\Requests\Database\DeleteDatabase;
use HelgeSverre\Chromadb\Requests\Database\GetDatabase;
use HelgeSverre\Chromadb\Requests\Database\ListDatabases;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Database extends BaseResource
{
    public function create(string $name, ?string $tenant = null): Response
    {
        return $this->connector->send(new CreateDatabase(
            $name,
            $tenant ?? $this->connector->getTenant()
        ));
    }

    public function get(string $database, ?string $tenant = null): Response
    {
        return $this->connector->send(new GetDatabase(
            $database,
            $tenant ?? $this->connector->getTenant()
        ));
    }

    public function list(?int $limit = null, ?int $offset = null, ?string $tenant = null): Response
    {
        return $this->connector->send(new ListDatabases(
            limit: $limit,
            offset: $offset,
            tenant: $tenant ?? $this->connector->getTenant()
        ));
    }

    public function delete(string $database, ?string $tenant = null): Response
    {
        return $this->connector->send(new DeleteDatabase(
            database: $database,
            tenant: $tenant ?? $this->connector->getTenant()
        ));
    }
}
