<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data as SpatieData;

/**
 * GetEmbedding
 */
class GetEmbedding extends SpatieData
{
    public function __construct(
        public mixed $ids = null,
        public mixed $where = null,
        #[MapName('where_document')]
        public mixed $whereDocument = null,
        public mixed $sort = null,
        public mixed $limit = null,
        public mixed $offset = null,
        public ?array $include = null,
    ) {
    }
}
