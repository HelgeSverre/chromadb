<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Tenant\CreateTenant;
use HelgeSverre\Chromadb\Requests\Tenant\GetTenant;
use HelgeSverre\Chromadb\Requests\Tenant\UpdateTenant;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Tenant extends BaseResource
{
    /**
     * Create a new tenant for multi-tenancy isolation.
     *
     * @param  string  $name  Name of the tenant to create
     * @return Response Response containing the created tenant details
     */
    public function create(string $name): Response
    {
        return $this->connector->send(new CreateTenant($name));
    }

    /**
     * Retrieve information about a specific tenant.
     *
     * @param  string  $tenant  Name of the tenant to retrieve
     * @return Response Response containing tenant details (name, configuration, etc.)
     */
    public function get(string $tenant): Response
    {
        return $this->connector->send(new GetTenant($tenant));
    }

    /**
     * Update a tenant's resource name.
     *
     * @deprecated Use setResourceName() for better clarity
     *
     * @param  string  $tenantName  Name of the tenant to update
     * @param  string  $resourceName  New resource name for the tenant (used in CRN lookups)
     * @return Response Response containing updated tenant details
     */
    public function update(string $tenantName, string $resourceName): Response
    {
        return $this->connector->send(new UpdateTenant(
            tenantName: $tenantName,
            resourceName: $resourceName
        ));
    }

    /**
     * Set a tenant's resource name (required for CRN-based collection lookups).
     *
     * The resource_name is used in CRN (Collection Resource Name) format:
     * resource_name:database_name:collection_name
     *
     * @param  string  $tenantName  Name of the tenant
     * @param  string  $resourceName  Resource name to set (used in CRN format)
     * @return Response Response containing updated tenant details
     */
    public function setResourceName(string $tenantName, string $resourceName): Response
    {
        return $this->update($tenantName, $resourceName);
    }
}
