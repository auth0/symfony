<?php

namespace Auth0\JWTAuthBundle;

use Auth0\JWTAuthBundle\DependencyInjection\Auth0Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Auth0\SDK\API\ApiClient;
use Auth0\SDK\API\InformationHeaders;
use Symfony\Component\HttpKernel\Kernel;

class JWTAuthBundle extends Bundle
{
	const SDK_VERSION = "1.2.3";

	public function __construct() {
		$oldInfoHeaders = ApiClient::getInfoHeadersData();

        if ($oldInfoHeaders) {
            $infoHeaders = InformationHeaders::Extend($oldInfoHeaders);
            
            $infoHeaders->setEnvironment('Symfony', Kernel::VERSION);
            $infoHeaders->setPackage('jwt-auth-bundle', self::SDK_VERSION);

            ApiClient::setInfoHeadersData($infoHeaders);
        }
	}

    public function getAlias()
    {
        return 'jwt_auth';
    }

}
