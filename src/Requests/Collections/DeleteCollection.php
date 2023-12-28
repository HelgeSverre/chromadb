<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * delete_collection
 */
class DeleteCollection extends Request
{
	protected Method $method = Method::DELETE;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections/{$this->collectionName}";
	}


	/**
	 * @param string $collectionName
	 * @param null|string $tenant
	 * @param null|string $database
	 */
	public function __construct(
		protected string $collectionName,
		protected ?string $tenant = null,
		protected ?string $database = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['tenant' => $this->tenant, 'database' => $this->database]);
	}
}
