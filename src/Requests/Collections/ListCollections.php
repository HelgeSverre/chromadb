<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * list_collections
 */
class ListCollections extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections";
	}


	/**
	 * @param null|mixed $limit
	 * @param null|mixed $offset
	 * @param null|string $tenant
	 * @param null|string $database
	 */
	public function __construct(
		protected mixed $limit = null,
		protected mixed $offset = null,
		protected ?string $tenant = null,
		protected ?string $database = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['limit' => $this->limit, 'offset' => $this->offset, 'tenant' => $this->tenant, 'database' => $this->database]);
	}
}
