<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * count
 */
class Count extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections/{$this->collectionId}/count";
	}


	/**
	 * @param string $collectionId
	 */
	public function __construct(
		protected string $collectionId,
	) {
	}
}
