<?php

declare(strict_types=1);

namespace Auth0\Symfony;

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Utility\HttpTelemetry;
use Auth0\Symfony\Contracts\BundleInterface;
use Auth0\Symfony\Security\Service;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Kernel;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class Auth0Bundle extends AbstractBundle implements BundleInterface
{
    public const SDK_VERSION = '5.0.0';

    public function __construct()
    {
        HttpTelemetry::setPackage('auth0/symfony', self::SDK_VERSION);
        HttpTelemetry::setEnvProperty('Symfony', Kernel::VERSION);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
            ->children()
                ->scalarNode('strategy')->defaultValue(SdkConfiguration::STRATEGY_API)->end()
                ->scalarNode('redirectUri')->end()
                ->scalarNode('domain')->cannotBeEmpty()->end()
                ->scalarNode('customDomain')->end()
                ->scalarNode('clientId')->cannotBeEmpty()->end()
                ->scalarNode('clientSecret')->end()
                ->scalarNode('audience')->end()
                ->scalarNode('scope')->end()
                ->scalarNode('organization')->end()
                ->scalarNode('cookieSecret')->end()
                ->integerNode('cookieExpires')->defaultNull(0)->min(-1)->end()
                ->scalarNode('cookieDomain')->end()
                ->scalarNode('cookiePath')->defaultValue('/')->end()
                ->booleanNode('cookieSecure')->defaultFalse()->end()
            ->end()
        ;
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $this->wireSdkConfiguration($config, $container, $builder);
        $this->wireSdk($config, $container, $builder);
        $this->wireSecurity($config, $container, $builder);
    }

    private function wireSdkConfiguration(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $builder
            ->register('auth0.sdk.configuration', SdkConfiguration::class)
                ->setPublic(true)
                ->setShared(false)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->addArgument('strategy')
                ->addArgument('redirectUri')
                ->addArgument('domain')
                ->addArgument('customDomain')
                ->addArgument('clientId')
                ->addArgument('clientSecret')
                ->addArgument('audience')
                ->addArgument('scope')
                ->addArgument('organization')
                ->addArgument('cookieSecret')
                ->addArgument('cookieExpires')
                ->addArgument('cookieDomain')
                ->addArgument('cookiePath')
                ->addArgument('cookieSecure')
            ;

        $container->services()
            ->set('auth0.sdk.configuration', SdkConfiguration::class)
            ->args([
                $config['strategy'] ?? SdkConfiguration::STRATEGY_API,
                $config['redirectUri'] ?? null,
                $config['domain'] ?? null,
                $config['customDomain'] ?? null,
                $config['clientId'] ?? null,
                $config['clientSecret'] ?? null,
                $config['audience'] ?? null,
                $config['scope'] ?? null,
                $config['organization'] ?? null,
                $config['cookieSecret'] ?? null,
                $config['cookieExpires'] ?? 0,
                $config['cookieDomain'] ?? null,
                $config['cookiePath'] ?? '/',
                $config['cookieSecure'] ?? false,
            ])
            ;
    }

    private function wireSdk(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $builder
            ->register('auth0.sdk', Auth0::class)
                ->setPublic(true)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->addArgument(new Reference('auth0.sdk.configuration'))
            ;

        $container
            ->services()
            ->set('auth0.sdk', Auth0::class)
            ->args([
                service('auth0.sdk.configuration'),
            ])
            ;
    }

    private function wireSecurity(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $builder
            ->register('auth0.security', Service::class)
                ->setPublic(true)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->addArgument(new Reference('auth0.sdk'))
            ;

        $container
            ->services()
            ->set('auth0.security', Service::class)
            ->args([
                service('auth0.sdk'),
            ])
            ;
    }
}
