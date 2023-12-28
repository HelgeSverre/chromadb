<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data as SpatieData;

/**
 * DeleteEmbedding
 */
class DeleteEmbedding extends SpatieData
{
	public function __construct(
		public mixed $ids = null,
		public mixed $where = null,
		#[MapName('where_document')]
		public mixed $whereDocument = null,
	) {
	}
}
