<?php

namespace HelgeSverre\Chromadb\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HelgeSverre\Chromadb\Chromadb
 */
class Chromadb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \HelgeSverre\Chromadb\Chromadb::class;
    }
}
