<?php

declare(strict_types=1);

namespace Auth0\Symfony\DependencyInjection;

use Auth0\SDK\Configuration\SdkConfiguration;
// use Happyr\Auth0Bundle\Security\Auth0EntryPoint;
// use Happyr\Auth0Bundle\Security\Auth0UserProviderInterface;
// use Happyr\Auth0Bundle\Security\Authentication\Auth0Authenticator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class Auth0Extension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->configureSdk($configuration, $container, $config['sdk'] ?? []);
        $this->configureFirewall($container, $config['firewall'] ?? []);
    }

    private function configureSdk(Configuration $configuration, ContainerBuilder $container, array $config)
    {
        $sdkDefinition = $container->getDefinition(SdkConfiguration::class);
        $sdkDefinition->setArgument('$configuration', null);

        foreach ($config as $key => $value) {
            if (\in_array($key, ['token_cache', 'http_client', 'http_request_factory', 'http_response_factory', 'http_stream_factory', 'session_storage', 'transient_storage', 'management_token_cache', 'event_listener_provider'], true)) {
                $value = new Reference($value);
            }

            $sdkDefinition->setArgument('$'.$key, $value);
        }
    }

    private function configureFirewall(ContainerBuilder $container, array $config)
    {
        if (! $config['enabled']) {
            $container->removeDefinition(Auth0Authenticator::class);
            $container->removeDefinition(Auth0EntryPoint::class);
            return;
        }

        if (null === $successHandler = $config['success_handler']) {
            $def = $container->setDefinition($successHandler = 'happyr_auth0.success_handler', new ChildDefinition('security.authentication.success_handler'));
            $def->replaceArgument(1, ['default_target_path' => $config['default_target_path']]);
        }

        if (null === $failureHandler = $config['failure_handler']) {
            $def = $container->setDefinition($failureHandler = 'happyr_auth0.failure_handler', new ChildDefinition('security.authentication.failure_handler'));
            $def->replaceArgument(2, ['failure_path' => $config['failure_path']]);
        }

        $def = $container->getDefinition(Auth0EntryPoint::class);
        $def->setArgument('$loginCheckRoute', $config['check_route']);
        $def->setArgument('$targetPathParameter', $config['target_path_parameter']);

        $def = $container->getDefinition(Auth0Authenticator::class);
        $def->setArgument('$loginCheckRoute', $config['check_route']);
        $def->addTag('container.service_subscriber', ['key' => AuthenticationFailureHandlerInterface::class, 'id' => $failureHandler]);
        $def->addTag('container.service_subscriber', ['key' => AuthenticationSuccessHandlerInterface::class, 'id' => $successHandler]);

        if (!empty($config['user_provider'])) {
            $def->addTag('container.service_subscriber', ['key' => Auth0UserProviderInterface::class, 'id' => $config['user_provider']]);
        }
    }
}
