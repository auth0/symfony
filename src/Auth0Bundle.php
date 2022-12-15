<?php

declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Auth0\Symfony\Contracts\BundleInterface;
use Auth0\Symfony\Controllers\AuthenticationController;
use Auth0\Symfony\Security\Authenticator;
use Auth0\Symfony\Security\Authorizer;
use Auth0\Symfony\Security\UserProvider;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class Auth0Bundle extends AbstractBundle implements BundleInterface
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $tokenCache = $config['sdk']['token_cache'] ?? null;
        $managementTokenCache = $config['sdk']['management_token_cache'] ?? null;
        $transientStorage = $config['sdk']['transient_storage'] ?? null;
        $sessionStorage = $config['sdk']['session_storage'] ?? null;
        $eventListenerProvider = $config['sdk']['event_listener_provider'] ?? null;

        if (null !== $tokenCache && '' !== $tokenCache) {
            $tokenCache = new Reference($tokenCache);
        }

        if (null !== $managementTokenCache && '' !== $managementTokenCache) {
            $managementTokenCache = new Reference($managementTokenCache);
        }

        if (null !== $transientStorage && '' !== $transientStorage) {
            $transientStorage = new Reference($transientStorage);
        }

        if (null !== $sessionStorage && '' !== $sessionStorage) {
            $sessionStorage = new Reference($sessionStorage);
        }

        if (null !== $eventListenerProvider && '' !== $eventListenerProvider) {
            $eventListenerProvider = new Reference($eventListenerProvider);
        }

        $audiences = $config['sdk']['audiences'] ?? [];
        $organizations = $config['sdk']['organizations'] ?? [];
        $scopes = $config['sdk']['scopes'] ?? [];

        if ([] === $audiences) {
            $audiences = null;
        }

        if ([] === $organizations) {
            $organizations = null;
        }

        if ([] === $scopes) {
            $scopes = null;
        }

        $container->services()
            ->set('auth0.configuration', SdkConfiguration::class)
                ->arg('$configuration', null)
                ->arg('$strategy', $config['sdk']['strategy'])
                ->arg('$domain', $config['sdk']['domain'])
                ->arg('$customDomain', $config['sdk']['custom_domain'])
                ->arg('$clientId', $config['sdk']['client_id'])
                ->arg('$redirectUri', $config['sdk']['redirect_uri'])
                ->arg('$clientSecret', $config['sdk']['client_secret'])
                ->arg('$audience', $audiences)
                ->arg('$organization', $organizations)
                ->arg('$usePkce', true)
                ->arg('$scope', $scopes)
                ->arg('$responseMode', 'query')
                ->arg('$responseType', 'code')
                ->arg('$tokenAlgorithm', $config['sdk']['token_algorithm'] ?? Token::ALGO_RS256)
                ->arg('$tokenJwksUri', $config['sdk']['token_jwks_uri'])
                ->arg('$tokenMaxAge', $config['sdk']['token_max_age'])
                ->arg('$tokenLeeway', $config['sdk']['token_leeway'] ?? 60)
                ->arg('$tokenCache', $tokenCache)
                ->arg('$tokenCacheTtl', $config['sdk']['token_cache_ttl'])
                ->arg('$httpClient', $config['sdk']['http_client'])
                ->arg('$httpMaxRetries', $config['sdk']['http_max_retries'])
                ->arg('$httpRequestFactory', $config['sdk']['http_request_factory'])
                ->arg('$httpResponseFactory', $config['sdk']['http_response_factory'])
                ->arg('$httpStreamFactory', $config['sdk']['http_stream_factory'])
                ->arg('$httpTelemetry', $config['sdk']['http_telemetry'])
                ->arg('$sessionStorage', $sessionStorage)
                ->arg('$sessionStorageId', $config['sdk']['session_storage_id'])
                ->arg('$cookieSecret', $config['sdk']['cookie_secret'])
                ->arg('$cookieDomain', $config['sdk']['cookie_domain'])
                ->arg('$cookieExpires', $config['sdk']['cookie_expires'])
                ->arg('$cookiePath', $config['sdk']['cookie_path'])
                ->arg('$cookieSameSite', $config['sdk']['cookie_same_site'])
                ->arg('$cookieSecure', $config['sdk']['cookie_secure'])
                ->arg('$persistUser', true)
                ->arg('$persistIdToken', true)
                ->arg('$persistAccessToken', true)
                ->arg('$persistRefreshToken', true)
                ->arg('$transientStorage', $transientStorage)
                ->arg('$transientStorageId', $config['sdk']['transient_storage_id'])
                ->arg('$queryUserInfo', false)
                ->arg('$managementToken', $config['sdk']['management_token'])
                ->arg('$managementTokenCache', $managementTokenCache)
                ->arg('$eventListenerProvider', $eventListenerProvider)
        ;

        $container->services()
            ->set('auth0', Service::class)
                ->arg('$configuration', new Reference('auth0.configuration'))
                ->arg('$logger', new Reference('logger'))
        ;

        $container->services()
            ->set('auth0.authenticator', Authenticator::class)
                ->arg('$configuration', $config['authenticator'] ?? [])
                ->arg('$service', new Reference('auth0'))
                ->arg('$router', new Reference('router'))
                ->arg('$logger', new Reference('logger'))
        ;

        $container->services()
            ->set('auth0.authorizer', Authorizer::class)
            ->arg('$configuration', $config['authorizer'] ?? [])
                ->arg('$service', new Reference('auth0'))
                ->arg('$logger', new Reference('logger'))
        ;

        $container->services()
            ->set(AuthenticationController::class)
                ->arg('$authenticator', new Reference('auth0.authenticator'))
                ->arg('$router', new Reference('router'))
                ->call('setContainer', [new Reference('service_container')])
                ->tag('controller.service_arguments')
        ;

        $container->services()
            ->set(UserProvider::class)
                ->arg('$service', new Reference('auth0'))
        ;
    }
}
