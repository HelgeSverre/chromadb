<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * get_database
 */
class GetDatabase extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/api/v1/databases/{$this->database}";
	}


	/**
	 * @param string $database
	 * @param null|string $tenant
	 */
	public function __construct(
		protected string $database,
		protected ?string $tenant = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['tenant' => $this->tenant]);
	}
}
