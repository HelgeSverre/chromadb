<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data as SpatieData;

/**
 * CreateCollection
 */
class CreateCollection extends SpatieData
{
    public function __construct(
        public ?string $name = null,
        public mixed $metadata = null,
        #[MapName('get_or_create')]
        public ?bool $getOrCreate = null,
    ) {
    }
}
