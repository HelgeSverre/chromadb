<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create_collection
 */
class CreateCollection extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/api/v1/collections";
	}


	/**
	 * @param null|string $tenant
	 * @param null|string $database
	 */
	public function __construct(
		protected ?string $tenant = null,
		protected ?string $database = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['tenant' => $this->tenant, 'database' => $this->database]);
	}
}
