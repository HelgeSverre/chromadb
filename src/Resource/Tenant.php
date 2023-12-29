<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Tenant\CreateTenant;
use HelgeSverre\Chromadb\Requests\Tenant\GetTenant;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Tenant extends BaseResource
{
    public function createTenant(string $name): Response
    {
        return $this->connector->send(new CreateTenant($name));
    }

    public function getTenant(string $tenant): Response
    {
        return $this->connector->send(new GetTenant($tenant));
    }
}
