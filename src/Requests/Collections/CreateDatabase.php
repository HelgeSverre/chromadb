<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create_database
 */
class CreateDatabase extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/api/v1/databases";
	}


	/**
	 * @param null|string $tenant
	 */
	public function __construct(
		protected ?string $tenant = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['tenant' => $this->tenant]);
	}
}
