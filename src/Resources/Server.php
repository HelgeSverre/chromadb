<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Server\Heartbeat;
use HelgeSverre\Chromadb\Requests\Server\PreFlightChecks;
use HelgeSverre\Chromadb\Requests\Server\Reset;
use HelgeSverre\Chromadb\Requests\Server\Version;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

class Server extends BaseResource
{
    /**
     * Resets the ChromaDB server.
     *
     * @return bool Returns true if the server was reset successfully, false otherwise.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function reset(): bool
    {
        $response = $this->connector->send(new Reset());

        if ($response->failed()) {
            return false;
        }

        return json_decode($response->body(), true);
    }

    /**
     * Gets the version of the ChromaDB server.
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function version(): string
    {
        $response = $this->connector->send(new Version());

        return json_decode($response->body(), true);

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
