<?php

namespace HelgeSverre\Chromadb\Resource;

use HelgeSverre\Chromadb\Requests\Server\Heartbeat;
use HelgeSverre\Chromadb\Requests\Server\PreFlightChecks;
use HelgeSverre\Chromadb\Requests\Server\Reset;
use HelgeSverre\Chromadb\Requests\Server\Version;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Server extends BaseResource
{
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
}
