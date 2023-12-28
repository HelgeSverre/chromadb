<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data as SpatieData;

/**
 * QueryEmbedding
 */
class QueryEmbedding extends SpatieData
{
	public function __construct(
		public mixed $where = null,
		#[MapName('where_document')]
		public mixed $whereDocument = null,
		#[MapName('query_embeddings')]
		public array $queryEmbeddings = null,
		#[MapName('n_results')]
		public int $nResults = null,
		public array $include = null,
	) {
	}
}
