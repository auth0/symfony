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
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $container->setParameter('jwt_auth.api_secret', $config['api_secret']);
        $container->setParameter('jwt_auth.domain', $config['domain']);

        $api_identifier = !empty($config['api_identifier_array']) ?
            // If we have an array of API identifiers, use that.
            $config['api_identifier_array'] :
            // Otherwise, use the original string value.
            $config['api_identifier'];

        // If we have a Client ID defined, add that to the API identifiers used.
        if (!empty($config['api_client_id'])) {
            $api_identifier = array_merge(
                is_array($api_identifier) ? $api_identifier : [$api_identifier],
                [$config['api_client_id']]
            );
        }
        $container->setParameter('jwt_auth.api_identifier', $api_identifier);

        $container->setParameter('jwt_auth.authorized_issuer', $config['authorized_issuer']);
        $container->setParameter('jwt_auth.secret_base64_encoded', $config['secret_base64_encoded']);
        $container->setParameter('jwt_auth.supported_algs', $config['supported_algs']);

        if (!empty($config['cache'])) {
            $ref = new Reference($config['cache']);
            $container->getDefinition('jwt_auth.auth0_service')
                ->replaceArgument(6, $ref);
        }
    }
}
