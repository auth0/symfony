<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('jwt_auth.domain', $config['domain']);
        $container->setParameter('jwt_auth.client_id', $config['client_id']);
        $container->setParameter('jwt_auth.client_secret', $config['client_secret']);
        $container->setParameter('jwt_auth.audience', $config['audience']);
        $container->setParameter('jwt_auth.authorized_issuer', $config['authorized_issuer']);
        $container->setParameter('jwt_auth.algorithm', $config['algorithm']);

        if (! empty($config['cache'])) {
            $cache = new Reference($config['cache']);

            $container->getDefinition('jwt_auth.auth0_service')
                      ->replaceArgument(7, $cache);
        }

        $validations = [
            'azp' => $config['client_id'],
            'aud' => $config['audience'],
            'leeway' => 60,
            'max_age' => null
        ];

        if (isset($config['validations'])) {
            if (array_key_exists('azp', $config['validations'])) {
                if (! empty($config['validations']['azp'])) {
                    if (true !== $config['validations']['azp']) {
                        $validations['azp'] = $config['validations']['azp'];
                    }
                } else {
                    $validations['azp'] = null;
                }
            }

            if (array_key_exists('aud', $config['validations'])) {
                if (! empty($config['validations']['aud'])) {
                    if (true !== $config['validations']['aud']) {
                        $validations['aud'] = $config['validations']['aud'];
                    }
                } else {
                    $validations['aud'] = null;
                }
            }

            if (array_key_exists('max_age', $config['validations'])) {
                if (! empty($config['validations']['max_age']) && is_int($config['validations']['max_age'])) {
                    $validations['max_age'] = $config['validations']['max_age'];
                } else {
                    $validations['max_age'] = null;
                }
            }

            if (array_key_exists('leeway', $config['validations'])) {
                if (! empty($config['validations']['leeway']) && is_int($config['validations']['leeway'])) {
                    $validations['leeway'] = $config['validations']['leeway'];
                } else {
                    $validations['leeway'] = 60;
                }
            }
        }

        $container->setParameter('jwt_auth.validations', $validations);
    }
}
