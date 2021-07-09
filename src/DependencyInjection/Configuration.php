<?php

declare(strict_types=1);

namespace WebsProjects\PayGatePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('websprojects_sylius_paygate_plugin');

        $rootNode = $treeBuilder->getRootNode();
       // $rootNode->

        $rootNode
            ->children()
                ->arrayNode('paygate')
                    ->children()
                        ->integerNode('paygate_id')->end()
                        ->scalarNode('reference')->end()
                        ->scalarNode('locale')->defaultValue('en-za')->end()
                        ->scalarNode('country')->defaultValue('ZAF')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
