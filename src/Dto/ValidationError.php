<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Data as SpatieData;

/**
 * ValidationError
 */
class ValidationError extends SpatieData
{
    public function __construct(
        public ?array $loc = null,
        public ?string $msg = null,
        public ?string $type = null,
    ) {
    }
}
