<?php

namespace HelgeSverre\Chromadb;

use HelgeSverre\Chromadb\Resource\CollectionOperations;
use HelgeSverre\Chromadb\Resource\VectorOperations;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use SensitiveParameter;

/**
 * Restful API
 */
class Chromadb extends Connector
{
    public function __construct(
        #[SensitiveParameter]
        protected readonly ?string $token,
    ) {
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }


}
