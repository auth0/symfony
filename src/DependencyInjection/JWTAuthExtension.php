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

class JWTAuthExtension extends Extension{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('jwt_auth.client_id', $config['client_id']);
        $container->setParameter('jwt_auth.client_secret', $config['client_secret']);
        $container->setParameter('jwt_auth.secret_base64_encoded', $config['secret_base64_encoded']);
    }

}
