<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * get_nearest_neighbors
 */
class GetNearestNeighbors extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections/{$this->collectionId}/query";
	}


	/**
	 * @param string $collectionId
	 */
	public function __construct(
		protected string $collectionId,
	) {
	}
}
