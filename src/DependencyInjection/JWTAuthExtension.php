<?php

declare(strict_types=1);

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Dependency injection extension for JWTAuthBundle.
 *
 * @package Auth0\JWTAuthBundle\DependencyInjection
 */
class JWTAuthExtension extends Extension
{
    /**
     * Loads the configuration for JWTAuthBundle.
     *
     * @param array<mixed>     $configs   Array containing the configuration values.
     * @param ContainerBuilder $container DI container for the bundle.
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('jwt_auth.domain', $config['domain']);
        $container->setParameter('jwt_auth.client_id', $config['client_id']);
        $container->setParameter('jwt_auth.client_secret', $config['client_secret']);
        $container->setParameter('jwt_auth.audience', $config['audience']);
        $container->setParameter('jwt_auth.authorized_issuer', $config['authorized_issuer']);
        $container->setParameter('jwt_auth.algorithm', $config['algorithm']);

        if (isset($config['cache'])) {
            $cache = new Reference($config['cache']);

            $container->getDefinition('jwt_auth.auth0_service')
                ->replaceArgument(7, $cache);
        }

        $validations = [
            'org_id' => null,
            'azp' => null,
            'aud' => $config['audience'],
            'leeway' => 60,
            'max_age' => null,
        ];

        if (isset($config['validations'])) {
            if (array_key_exists('azp', $config['validations'])) {
                if (isset($config['validations']['azp'])) {
                    if ($config['validations']['azp'] === true) {
                        $validations['azp'] = $config['client_id'];
                    } else {
                        $validations['azp'] = $config['validations']['azp'];
                    }
                } else {
                    $validations['azp'] = null;
                }
            }

            if (array_key_exists('aud', $config['validations'])) {
                if (isset($config['validations']['aud'])) {
                    if ($config['validations']['aud'] !== true) {
                        $validations['aud'] = $config['validations']['aud'];
                    }
                } else {
                    $validations['aud'] = null;
                }
            }

            if (array_key_exists('org_id', $config['validations'])) {
                if (isset($config['validations']['org_id'])) {
                    if ($config['validations']['org_id'] !== true) {
                        $validations['org_id'] = $config['validations']['org_id'];
                    }
                } else {
                    $validations['org_id'] = null;
                }
            }

            if (array_key_exists('max_age', $config['validations'])) {
                if (isset($config['validations']['max_age']) && is_int($config['validations']['max_age'])) {
                    $validations['max_age'] = $config['validations']['max_age'];
                } else {
                    $validations['max_age'] = null;
                }
            }

            if (array_key_exists('leeway', $config['validations'])) {
                if (isset($config['validations']['leeway']) && is_int($config['validations']['leeway'])) {
                    $validations['leeway'] = $config['validations']['leeway'];
                } else {
                    $validations['leeway'] = 60;
                }
            }
        }

        $container->setParameter('jwt_auth.validations', $validations);
    }
}
