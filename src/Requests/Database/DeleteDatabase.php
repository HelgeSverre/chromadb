<?php

namespace HelgeSverre\Chromadb\Requests\Database;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Deletes a database.
 */
class DeleteDatabase extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $database,
        protected ?string $tenant = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $tenant = $this->tenant ?? 'default_tenant';

        return "/api/v2/tenants/{$tenant}/databases/{$this->database}";
    }
}
