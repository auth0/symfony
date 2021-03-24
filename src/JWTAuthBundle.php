<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\API\Helpers\InformationHeaders;

/**
 * A simple JWT Authentication Bundle for Symfony REST APIs.
 *
 * @package Auth0\JWTAuthBundle
 */
class JWTAuthBundle extends Bundle
{
    const SDK_VERSION = '4.0.0';

    /**
     * JWTAuthBundle constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $oldInfoHeaders = ApiClient::getInfoHeadersData();

        if ($oldInfoHeaders) {
            $infoHeaders = InformationHeaders::Extend($oldInfoHeaders);

            $infoHeaders->setEnvProperty('Symfony', Kernel::VERSION);
            $infoHeaders->setPackage('jwt-auth-bundle', self::SDK_VERSION);

            ApiClient::setInfoHeadersData($infoHeaders);
        }
    }

    /**
     * Returns an alias for the JWTAuthBundle
     *
     * @return string
     */
    public function getAlias(): string
    {
        return 'jwt_auth';
    }
}
