<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Data as SpatieData;

/**
 * AddEmbedding
 */
class AddEmbedding extends SpatieData
{
    public function __construct(
        public mixed $embeddings = null,
        public mixed $metadatas = null,
        public mixed $documents = null,
        public mixed $uris = null,
        public ?array $ids = null,
    ) {
    }
}
