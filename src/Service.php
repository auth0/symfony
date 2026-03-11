<?php

declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Utility\HttpTelemetry;
use Auth0\Symfony\Contracts\ServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

final class Service implements ServiceInterface
{
    public const VERSION = '5.6.0';

    private ?Auth0 $sdk = null;

    public function __construct(
        private SdkConfiguration $configuration,
        private RequestStack $requestStack,
        private LoggerInterface $logger,
    ) {
    }

    public function getSdk(): Auth0
    {
        if (! $this->sdk instanceof Auth0) {
            $this->warmUp();
            $this->sdk = new Auth0($this->configuration);

            HttpTelemetry::setEnvProperty('Symfony', Kernel::VERSION);
            HttpTelemetry::setPackage('symfony', self::VERSION);
        }

        return $this->sdk;
    }

    public function warmUp(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            $this->configuration->getTokenCache();
            $this->configuration->getManagementTokenCache();
            $this->configuration->getSessionStorage();
            $this->configuration->getTransientStorage();
        }
    }
}
