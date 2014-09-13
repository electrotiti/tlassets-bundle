<?php

namespace TlAssetsBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tl_assets');

        $rootNode->children()
                    ->booleanNode('debug')
                    ->defaultValue('%kernel.debug%')
                 ->end()
                    ->arrayNode('filters')
                        ->children()
                            ->booleanNode('hash')->end()
                            ->booleanNode('minify')->end()
                            ->booleanNode('concat')->end()
                ->end();

        return $treeBuilder;
    }
}
