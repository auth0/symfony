<?php

namespace Auth0\JWTAuthBundle;

use Auth0\JWTAuthBundle\DependencyInjection\Auth0Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Auth0\SDK\API\ApiClient;
use Symfony\Component\HttpKernel\Kernel;

class JWTAuthBundle extends Bundle
{
	const SDK_VERSION = "1.2.1";

	public function __construct() {
		ApiClient::addHeaderInfoMeta('Symfony:'.Kernel::VERSION);
		ApiClient::addHeaderInfoMeta('SDK:'.self::SDK_VERSION);
	}

    public function getAlias()
    {
        return 'jwt_auth';
    }

}
