<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Tenant\CreateTenant;
use HelgeSverre\Chromadb\Requests\Tenant\GetTenant;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Tenant extends BaseResource
{
    public function create(string $name): Response
    {
        return $this->connector->send(new CreateTenant($name));
    }

    public function get(string $tenant): Response
    {
        return $this->connector->send(new GetTenant($tenant));
    }
}
