<?php
/**
 * Created by PhpStorm.
 * User: german
 * Date: 1/21/15
 * Time: 9:24 PM
 */

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class Auth0JWTAuthExtension extends Extension{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('auth0_jwt_auth.client_id', $config['client_id']);
        $container->setParameter('auth0_jwt_auth.client_secret', $config['client_secret']);
        $container->setParameter('auth0_jwt_auth.domain', $config['domain']);
        $container->setParameter('auth0_jwt_auth.redirect_url', $config['redirect_url']);
    }

} 