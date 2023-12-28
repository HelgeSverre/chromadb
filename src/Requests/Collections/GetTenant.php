<?php

namespace HelgeSverre\Chromadb\Requests\Collections;

use DateTime;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * get_tenant
 */
class GetTenant extends Request
{
	protected Method $method = Method::GET;


	public function resolveEndpoint(): string
	{
		return "/api/v1/tenants/{$this->tenant}";
	}


	/**
	 * @param string $tenant
	 */
	public function __construct(
		protected string $tenant,
	) {
	}
}
