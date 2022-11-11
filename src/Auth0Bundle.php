<?php declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\Utility\HttpTelemetry;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Kernel;

class Auth0Bundle extends AbstractBundle
{
    const SDK_VERSION = '5.0.0';

    public function __construct()
    {
        HttpTelemetry::setEnvProperty('Symfony', Kernel::VERSION);
        HttpTelemetry::setPackage('jwt-auth-bundle', self::SDK_VERSION);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/auth0.php');
    }
}
