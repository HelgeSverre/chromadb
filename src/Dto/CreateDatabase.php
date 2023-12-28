<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Data as SpatieData;

/**
 * CreateDatabase
 */
class CreateDatabase extends SpatieData
{
	public function __construct(
		public string $name = null,
	) {
	}
}
