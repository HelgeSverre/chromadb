<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * delete
 */
class Delete extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections/{$this->collectionId}/delete";
	}


	/**
	 * @param string $collectionId
	 */
	public function __construct(
		protected string $collectionId,
	) {
	}
}
