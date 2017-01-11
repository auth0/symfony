<?php

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author german
 */
class JWTAuthExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('jwt_auth.api_secret', $config['api_secret']);
        $container->setParameter('jwt_auth.domain', $config['domain']);
        $container->setParameter('jwt_auth.api_identifier', $config['api_identifier']);
        $container->setParameter('jwt_auth.authorized_issuer', $config['authorized_issuer']);
        $container->setParameter('jwt_auth.secret_base64_encoded', $config['secret_base64_encoded']);
        $container->setParameter('jwt_auth.supported_algs', $config['supported_algs']);
    }
}
