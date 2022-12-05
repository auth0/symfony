<?php

declare(strict_types=1);

namespace Auth0\Symfony\DependencyInjection;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('auth0');

        /** @var ArrayNodeDefinition $root */
        $root = $builder->getRootNode();

        $root
            ->children()
                ->arrayNode('sdk')
                    ->children()
                        ->scalarNode('strategy')->defaultValue(SdkConfiguration::STRATEGY_API)->end()
                        ->scalarNode('domain')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('custom_domain')->end()
                        ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('redirect_uri')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('audiences')->scalarPrototype()->end()->end()
                        ->arrayNode('organizations')->scalarPrototype()->end()->end()
                        ->arrayNode('scopes')->scalarPrototype()->end()->end()
                        ->scalarNode('token_algorithm')->defaultValue(Token::ALGO_RS256)->end()
                        ->scalarNode('token_jwks_uri')->defaultNull()->end()
                        ->scalarNode('token_max_age')->defaultNull()->end()
                        ->integerNode('token_leeway')->defaultValue(60)->end()
                        // ->scalarNode('token_cache')->defaultNull()->end()
                        ->integerNode('token_cache_ttl')->defaultValue(60)->end()
                        // ->scalarNode('http_client')->defaultNull()->end()
                        ->integerNode('http_max_retries')->defaultValue(3)->end()
                        // ->scalarNode('http_request_factory')->defaultNull()->end()
                        // ->scalarNode('http_response_factory')->defaultNull()->end()
                        // ->scalarNode('http_stream_factory')->defaultNull()->end()
                        ->booleanNode('http_telemetry')->defaultTrue()->end()
                        // ->scalarNode('session_storage')->defaultNull()->end()
                        ->scalarNode('session_storage_id')->defaultValue('auth0_session')->end()
                        ->scalarNode('cookie_secret')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('cookie_domain')->defaultNull()->end()
                        ->integerNode('cookie_expires')->defaultValue(0)->end()
                        ->scalarNode('cookie_path')->defaultNull('/')->end()
                        ->scalarNode('cookie_samesite')->defaultNull('lax')->end()
                        ->booleanNode('cookie_secure')->defaultFalse()->end()
                        // ->scalarNode('transient_storage')->defaultNull()->end()
                        ->scalarNode('transient_storage_id')->defaultValue('auth0_transient')->end()
                        ->scalarNode('management_token')->defaultNull()->end()
                        // ->scalarNode('management_token_cache')->defaultNull()->end()
                        // ->scalarNode('event_listener_provider')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('firewall')->canBeEnabled()
                    ->children()
                        ->scalarNode('user_provider')->defaultNull()->end()
                        ->arrayNode('routes')
                            ->children()
                                ->scalarNode('callback')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('successful')->defaultNull()->end()
                                ->scalarNode('failure')->defaultNull()->end()
                            ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
