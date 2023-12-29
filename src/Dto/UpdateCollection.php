<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data as SpatieData;

/**
 * UpdateCollection
 */
class UpdateCollection extends SpatieData
{
    public function __construct(
        #[MapName('new_name')]
        public mixed $newName = null,
        #[MapName('new_metadata')]
        public mixed $newMetadata = null,
    ) {
    }
}
