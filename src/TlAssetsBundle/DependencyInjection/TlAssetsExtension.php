<?php

namespace TlAssetsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TlAssetsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('tl_assets.debug', $config['debug']);
        $container->setParameter('tl_assets.live_compilation', $config['live_compilation']);
        $container->setParameter('tl_assets.bundles', $config['bundles']);
        $container->setParameter('tl_assets.use_cache', $config['use_cache']);
        $container->setParameter('tl_assets.variables', $config['variables']);

        $defaultVal = $config['filters'];
        $filters = array();

        if( (!array_key_exists('hash',$defaultVal) && !$config['debug']) ||
            (array_key_exists('hash',$defaultVal) && $defaultVal['hash']) ) {
            $filters[] = 'hash';
        }

        if( (!array_key_exists('minify',$defaultVal) && !$config['debug']) ||
            (array_key_exists('minify',$defaultVal) && $defaultVal['minify']) ) {
            $filters[] = 'minify';
        }

        if( (!array_key_exists('concat',$defaultVal) && !$config['debug']) ||
            (array_key_exists('concat',$defaultVal) && $defaultVal['concat']) ) {
            $filters[] = 'concat';
        }

        $container->setParameter('tl_assets.default_filters', $filters);
    }
}
