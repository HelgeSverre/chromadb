<?php

namespace HelgeSverre\Chromadb\Dto;

use Spatie\LaravelData\Data as SpatieData;

/**
 * HTTPValidationError
 */
class HttpvalidationError extends SpatieData
{
	public function __construct(
		public array $detail = null,
	) {
	}
}
