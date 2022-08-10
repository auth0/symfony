<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle;

// use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\Utility\HttpClient;
use Auth0\SDK\Utility\HttpTelemetry;
// use Auth0\SDK\API\Helpers\InformationHeaders;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

/**
 * A simple JWT Authentication Bundle for Symfony REST APIs.
 *
 * @package Auth0\JWTAuthBundle
 */
class JWTAuthBundle extends Bundle
{
    public const VERSION = '5.0.0';

    /**
     * JWTAuthBundle constructor.
     *
     * @return void
     */
    public function __construct()
    {
        HttpTelemetry::setPackage('jwt-auth-bundle', self::VERSION);
        HttpTelemetry::setEnvProperty('Symfony', Kernel::VERSION);
    }

    /**
     * Returns an alias for the JWTAuthBundle
     *
     * @psalm-return 'jwt_auth'
     */
    public function getAlias(): string
    {
        return 'jwt_auth';
    }
}

