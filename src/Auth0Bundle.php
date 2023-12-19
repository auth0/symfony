<?php

declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\StoreInterface;
use Auth0\SDK\Token;
use Auth0\Symfony\Contracts\BundleInterface;
use Auth0\Symfony\Controllers\AuthenticationController;
use Auth0\Symfony\Security\{Authenticator, Authorizer, UserProvider};
use Auth0\Symfony\Stores\SessionStore;
use LogicException;
use OpenSSLAsymmetricKey;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Reference};
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class Auth0Bundle extends AbstractBundle implements BundleInterface
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array<mixed> $config The configuration array.
     * @param ContainerConfigurator $container The container configurator.
     * @param ContainerBuilder $builder The container builder.
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $sdkConfig = $config['sdk'] ?? [];

        /**
         * @var array{strategy: string, domain: ?string, custom_domain: ?string, client_id: ?string, redirect_uri: ?string, client_secret: ?string, audiences: null|array<string>, organizations: array<string>|null, use_pkce: bool, scopes: array<string>|null, response_mode: string, response_type: string, token_algorithm: ?string, token_jwks_uri: ?string, token_max_age: ?int, token_leeway: ?int, token_cache: ?CacheItemPoolInterface, token_cache_ttl: int, http_client: null|string|ClientInterface, http_max_retries: int, http_request_factory: null|string|RequestFactoryInterface, http_response_factory: null|string|ResponseFactoryInterface, http_stream_factory: null|string|StreamFactoryInterface, http_telemetry: bool, session_storage: ?StoreInterface, session_storage_prefix: ?string, cookie_secret: ?string, cookie_domain: ?string, cookie_expires: int, cookie_path: string, cookie_secure: bool, cookie_same_site: ?string, persist_user: bool, persist_id_token: bool, persist_access_token: bool, persist_refresh_token: bool, transient_storage: ?StoreInterface, transient_storage_prefix: ?string, query_user_info: bool, management_token: ?string, management_token_cache: ?CacheItemPoolInterface, event_listener_provider: null|string|ListenerProviderInterface, client_assertion_signing_key: null|OpenSSLAsymmetricKey|string, client_assertion_signing_algorithm: string, pushed_authorization_request: bool, backchannel_logout_cache: ?CacheItemPoolInterface, backchannel_logout_expires: int} $sdkConfig
         */

        $tokenCache = $sdkConfig['token_cache'] ?? 'cache.app';

        if (! $tokenCache instanceOf CacheItemPoolInterface) {
            $tokenCache = new Reference($tokenCache);
        }

        $managementTokenCache = $sdkConfig['management_token_cache'] ?? 'cache.app';

        if (! $managementTokenCache instanceOf CacheItemPoolInterface) {
            $managementTokenCache = new Reference($managementTokenCache);
        }

        $backchannelLogoutCache = $sdkConfig['backchannel_logout_cache'] ?? 'cache.app';

        if (! $backchannelLogoutCache instanceOf CacheItemPoolInterface) {
            $backchannelLogoutCache = new Reference($backchannelLogoutCache);
        }

        $transientStorage = $sdkConfig['transient_storage'] ?? 'auth0.store_transient';

        if (! $transientStorage instanceOf StoreInterface) {
            $transientStorage = new Reference($transientStorage);
        }

        $sessionStorage = $sdkConfig['session_storage'] ?? 'auth0.store_session';

        if (! $sessionStorage instanceOf StoreInterface) {
            $sessionStorage = new Reference($sessionStorage);
        }

        $transientStoragePrefix = $sdkConfig['transient_storage_prefix'] ?? 'auth0_transient';
        $sessionStoragePrefix = $sdkConfig['session_storage_prefix'] ?? 'auth0_session';

        $eventListenerProvider = $sdkConfig['event_listener_provider'] ?? null;

        if (! $eventListenerProvider instanceOf ListenerProviderInterface && $eventListenerProvider !== '' && $eventListenerProvider !== null) {
            $eventListenerProvider = new Reference($eventListenerProvider);
        }

        $httpClient = $sdkConfig['http_client'] ?? null;

        if (! $httpClient instanceOf ClientInterface && $httpClient !== '' && $httpClient !== null) {
            $httpClient = new Reference($httpClient);
        }

        $httpRequestFactory = $sdkConfig['http_request_factory'] ?? null;

        if (! $httpRequestFactory instanceOf RequestFactoryInterface && $httpRequestFactory !== '' && $httpRequestFactory !== null) {
            $httpRequestFactory = new Reference($httpRequestFactory);
        }

        $httpResponseFactory = $sdkConfig['http_response_factory'] ?? null;

        if (! $httpResponseFactory instanceOf ResponseFactoryInterface && $httpResponseFactory !== '' && $httpResponseFactory !== null) {
            $httpResponseFactory = new Reference($httpResponseFactory);
        }

        $httpStreamFactory = $sdkConfig['http_stream_factory'] ?? null;

        if (! $httpStreamFactory instanceOf StreamFactoryInterface && $httpStreamFactory !== '' && $httpStreamFactory !== null) {
            $httpStreamFactory = new Reference($httpStreamFactory);
        }

        $audiences = $sdkConfig['audiences'] ?? [];
        $organizations = $sdkConfig['organizations'] ?? [];
        $scopes = $sdkConfig['scopes'] ?? [];

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
            ->arg('$strategy', $sdkConfig['strategy'])
            ->arg('$domain', $sdkConfig['domain'])
            ->arg('$customDomain', $sdkConfig['custom_domain'])
            ->arg('$clientId', $sdkConfig['client_id'])
            ->arg('$redirectUri', $sdkConfig['redirect_uri'])
            ->arg('$clientSecret', $sdkConfig['client_secret'])
            ->arg('$audience', $audiences)
            ->arg('$organization', $organizations)
            ->arg('$usePkce', true)
            ->arg('$scope', $scopes)
            ->arg('$responseMode', 'query')
            ->arg('$responseType', 'code')
            ->arg('$tokenAlgorithm', $sdkConfig['token_algorithm'] ?? Token::ALGO_RS256)
            ->arg('$tokenJwksUri', $sdkConfig['token_jwks_uri'])
            ->arg('$tokenMaxAge', $sdkConfig['token_max_age'])
            ->arg('$tokenLeeway', $sdkConfig['token_leeway'] ?? 60)
            ->arg('$tokenCache', $tokenCache)
            ->arg('$tokenCacheTtl', $sdkConfig['token_cache_ttl'])
            ->arg('$httpClient', $httpClient)
            ->arg('$httpMaxRetries', $sdkConfig['http_max_retries'])
            ->arg('$httpRequestFactory', $httpRequestFactory)
            ->arg('$httpResponseFactory', $httpResponseFactory)
            ->arg('$httpStreamFactory', $httpStreamFactory)
            ->arg('$httpTelemetry', $sdkConfig['http_telemetry'])
            ->arg('$sessionStorage', $sessionStorage)
            ->arg('$sessionStorageId', $sessionStoragePrefix)
            ->arg('$cookieSecret', $sdkConfig['cookie_secret'])
            ->arg('$cookieDomain', $sdkConfig['cookie_domain'])
            ->arg('$cookieExpires', $sdkConfig['cookie_expires'])
            ->arg('$cookiePath', $sdkConfig['cookie_path'])
            ->arg('$cookieSameSite', $sdkConfig['cookie_same_site'])
            ->arg('$cookieSecure', $sdkConfig['cookie_secure'])
            ->arg('$persistUser', true)
            ->arg('$persistIdToken', true)
            ->arg('$persistAccessToken', true)
            ->arg('$persistRefreshToken', true)
            ->arg('$transientStorage', $transientStorage)
            ->arg('$transientStorageId', $transientStoragePrefix)
            ->arg('$queryUserInfo', false)
            ->arg('$managementToken', $sdkConfig['management_token'])
            ->arg('$managementTokenCache', $managementTokenCache)
            ->arg('$eventListenerProvider', $eventListenerProvider)
            ->arg('$backchannelLogoutCache', $backchannelLogoutCache)
            ->arg('$backchannelLogoutExpires', $sdkConfig['backchannel_logout_expires']);

        $container->services()
            ->set('auth0', Service::class)
            ->arg('$configuration', new Reference('auth0.configuration'))
            ->arg('$requestStack', new Reference('request_stack'))
            ->arg('$logger', new Reference('logger'))
            ->tag('routing.condition_service');

        $container->services()
            ->set('auth0.authenticator', Authenticator::class)
            ->arg('$configuration', $config['authenticator'] ?? [])
            ->arg('$service', new Reference('auth0'))
            ->arg('$router', new Reference('router'))
            ->arg('$logger', new Reference('logger'))
            ->tag('security.authenticator');

        $container->services()
            ->set('auth0.store_session', SessionStore::class)
            ->arg('$namespace', $sessionStoragePrefix)
            ->arg('$requestStack', new Reference('request_stack'))
            ->arg('$logger', new Reference('logger'));

        $container->services()
            ->set('auth0.store_transient', SessionStore::class)
            ->arg('$namespace', $transientStoragePrefix)
            ->arg('$requestStack', new Reference('request_stack'))
            ->arg('$logger', new Reference('logger'));

        $container->services()
            ->set('auth0.authorizer', Authorizer::class)
            ->arg('$configuration', $config['authorizer'] ?? [])
            ->arg('$service', new Reference('auth0'))
            ->arg('$logger', new Reference('logger'));

        $container->services()
            ->set(AuthenticationController::class)
            ->arg('$authenticator', new Reference('auth0.authenticator'))
            ->arg('$router', new Reference('router'))
            ->call('setContainer', [new Reference('service_container')])
            ->tag('controller.service_arguments');

        $container->services()
            ->set(UserProvider::class)
            ->arg('$service', new Reference('auth0'));
    }
}
