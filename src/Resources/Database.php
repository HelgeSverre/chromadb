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
    /**
     * Create a new database within a tenant.
     *
     * @param  string  $name  Name of the database to create
     * @param  string|null  $tenant  Override default tenant
     * @return Response Response containing the created database details
     */
    public function create(string $name, ?string $tenant = null): Response
    {
        return $this->connector->send(new CreateDatabase(
            $name,
            $tenant ?? $this->connector->getTenant()
        ));
    }

    /**
     * Retrieve information about a specific database.
     *
     * @param  string  $database  Name of the database to retrieve
     * @param  string|null  $tenant  Override default tenant
     * @return Response Response containing database details (name, tenant, etc.)
     */
    public function get(string $database, ?string $tenant = null): Response
    {
        return $this->connector->send(new GetDatabase(
            $database,
            $tenant ?? $this->connector->getTenant()
        ));
    }

    /**
     * List all databases in the specified tenant.
     *
     * @param  int|null  $limit  Maximum number of databases to return (pagination)
     * @param  int|null  $offset  Number of databases to skip (pagination)
     * @param  string|null  $tenant  Override default tenant
     * @return Response Response containing array of database objects
     */
    public function list(?int $limit = null, ?int $offset = null, ?string $tenant = null): Response
    {
        return $this->connector->send(new ListDatabases(
            limit: $limit,
            offset: $offset,
            tenant: $tenant ?? $this->connector->getTenant()
        ));
    }

    /**
     * Delete a database from the specified tenant.
     *
     * @param  string  $database  Name of the database to delete
     * @param  string|null  $tenant  Override default tenant
     * @return Response Response confirming deletion
     */
    public function delete(string $database, ?string $tenant = null): Response
    {
        return $this->connector->send(new DeleteDatabase(
            database: $database,
            tenant: $tenant ?? $this->connector->getTenant()
        ));
    }
}
