<?php

namespace Youshido\DoctrineExtensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Youshido\DoctrineExtensionBundle\AesEncrypt\Type\AesEncryptedType;
use Youshido\DoctrineExtensionBundle\Scopable\Filter\ScopableFilter;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const KEY = 'youshido_doctrine_extension_config';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('youshido_doctrine_extension');

        $rootNode
            ->children()
                ->arrayNode(AesEncryptedType::NAME)
                    ->children()
                        ->scalarNode('key')->end()
                    ->end()
                ->end()
                ->booleanNode(ScopableFilter::NAME)->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
