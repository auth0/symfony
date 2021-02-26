<?php

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author german
 */
class JWTAuthExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('jwt_auth.domain', $config['domain']);
        $container->setParameter('jwt_auth.client_id', $config['client_id']);
        $container->setParameter('jwt_auth.audience', $config['audience']);
        $container->setParameter('jwt_auth.authorized_issuer', $config['authorized_issuer']);
    }
}
