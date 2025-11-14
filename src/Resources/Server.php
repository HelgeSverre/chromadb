<?php

namespace HelgeSverre\Chromadb\Resources;

use HelgeSverre\Chromadb\Requests\Server\GetUserIdentity;
use HelgeSverre\Chromadb\Requests\Server\Healthcheck;
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
     * Reset the ChromaDB server (WARNING: Deletes all data).
     *
     * CAUTION: This operation removes all collections, databases, and data from the server.
     * Only use this in development/testing environments.
     *
     * @return bool Returns true if the server was reset successfully, false otherwise
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function reset(): bool
    {
        $response = $this->connector->send(new Reset);

        if ($response->failed()) {
            return false;
        }

        return true;
    }

    /**
     * Get the version of the ChromaDB server.
     *
     * @return string Server version string (e.g., "0.4.15")
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function version(): string
    {
        $response = $this->connector->send(new Version);

        return json_decode($response->body(), true);

    }

    /**
     * Get server heartbeat with nanosecond timestamp.
     *
     * Used to verify the server is responsive and get precise timing information.
     *
     * @return Response Response containing nanosecond heartbeat timestamp
     */
    public function heartbeat(): Response
    {
        return $this->connector->send(new Heartbeat);
    }

    /**
     * Perform pre-flight checks on the server.
     *
     * Used to verify server configuration and readiness before operations.
     *
     * @return Response Response containing pre-flight check results
     */
    public function preFlightChecks(): Response
    {
        return $this->connector->send(new PreFlightChecks);
    }

    /**
     * Check the health status of the ChromaDB server.
     *
     * @return Response Response with HTTP 200 if healthy, error otherwise
     */
    public function healthcheck(): Response
    {
        return $this->connector->send(new Healthcheck);
    }

    /**
     * Get current user identity and permissions.
     *
     * Returns information about the authenticated user including user_id, tenant, and accessible databases.
     *
     * @return Response Response containing user identity (user_id, tenant, databases)
     */
    public function identity(): Response
    {
        return $this->connector->send(new GetUserIdentity);
    }
}
