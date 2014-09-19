<?php

namespace TlAssetsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tl_assets');

        $rootNode->children()
                    ->booleanNode('debug')->defaultValue('%kernel.debug%')->end()
                    ->booleanNode('live_compilation')->defaultValue(false)->end()
                    ->booleanNode('use_cache')->defaultValue(false)->end()

                    ->arrayNode('bundles')
                        ->prototype('scalar')->end()
                    ->end()

                    ->arrayNode('variables')
                        ->prototype('scalar')->end()
                    ->end()

                    ->arrayNode('filters')
                        ->children()
                            ->booleanNode('hash')->end()
                            ->booleanNode('minify')->end()
                            ->booleanNode('concat')->end()
                        ->end()
                    ->end()
                   ->end();

        return $treeBuilder;
    }
}
