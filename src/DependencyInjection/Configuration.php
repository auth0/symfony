<?php declare(strict_types=1);

namespace Auth0\JWTAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jwt_auth');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('domain')->defaultValue('')->end()
                ->scalarNode('client_id')->defaultValue('')->end()
                ->scalarNode('client_secret')->defaultValue('')->end()
                ->arrayNode('audience')
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->castToArray()->end()
                ->end()
                ->scalarNode('authorized_issuer')->defaultValue('')->end()
                ->scalarNode('cache')->defaultNull()->end()
                ->enumNode('algorithm')->defaultValue('RS256')->values(['RS256', 'HS256'])->end()
                ->arrayNode('validations')
                ->children()
                    ->scalarNode('azp')->defaultValue(false)->end()
                    ->scalarNode('aud')->defaultValue(true)->end()
                    ->scalarNode('leeway')->defaultValue(60)->end()
                    ->scalarNode('max_age')->defaultValue('')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
