<?php

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jwt_auth');

        $rootNode
            ->children()
            ->scalarNode('api_secret')->defaultValue('')->end()
            ->scalarNode('domain')->defaultValue('')->end()
            ->scalarNode('api_identifier')->defaultValue('')->end()
            ->scalarNode('api_client_id')->defaultValue('')->end()
            ->arrayNode('api_identifier_array')
                ->prototype('scalar')->end()
            ->end()
            ->scalarNode('authorized_issuer')->defaultValue('')->end()
            ->arrayNode('supported_algs')
                ->addDefaultChildrenIfNoneSet(1)
                ->prototype('scalar')
                ->defaultValue('RS256')
                ->end()
            ->end()
            ->scalarNode('secret_base64_encoded')->defaultValue(false)->end()
            ->scalarNode('cache')->defaultNull()->info('The cache service you want to use. Example "jwt_auth.cache.file_system".')->end();

        return $treeBuilder;
    }
}
