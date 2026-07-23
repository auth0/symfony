<?php

declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\API\Management\Wrapper\{ManagementClient, ManagementClientOptions};
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Utility\HttpTelemetry;
use Auth0\Symfony\Contracts\ServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

final class Service implements ServiceInterface
{
    public const VERSION = '6.0.0-beta.0';

    private ?ManagementClient $management = null;

    private ?Auth0 $sdk = null;

    public function __construct(
        private SdkConfiguration $configuration,
        private RequestStack $requestStack,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Return a v9 Management API client built from the bundle's existing
     * configuration. Use this instead of the v8-style `getSdk()->management()`,
     * which is non-functional in auth0-php v9.
     *
     * The wrapper resolves its token in this order: a configured static
     * `management_token` if present, otherwise a client-credentials token
     * fetched and cached via the `management_token_cache` pool. Sub-clients are
     * reached by property access, e.g. `$service->getManagement()->users->list()`.
     */
    public function getManagement(): ManagementClient
    {
        if (! $this->management instanceof ManagementClient) {
            $this->warmUp();

            $this->management = new ManagementClient(new ManagementClientOptions(
                domain: (string) $this->configuration->getDomain(),
                token: $this->configuration->getManagementToken(),
                clientId: $this->configuration->getClientId(),
                clientSecret: $this->configuration->getClientSecret(),
                tokenCache: $this->configuration->getManagementTokenCache(),
            ));
        }

        return $this->management;
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
