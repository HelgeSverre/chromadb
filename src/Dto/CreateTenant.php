<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Data as SpatieData;

/**
 * CreateTenant
 */
class CreateTenant extends SpatieData
{
    public function __construct(
        public ?string $name = null,
    ) {
    }
}
