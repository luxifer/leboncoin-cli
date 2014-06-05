<?php
namespace Luxifer\Leboncoin\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class MailerConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mailer');

        $rootNode
            ->children()
                ->arrayNode('swift')
                    ->children()
                        ->scalarNode('type')->defaultValue('smtp')->isRequired()->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->integerNode('port')->defaultValue(25)->end()
                        ->scalarNode('user')->defaultNull()->end()
                        ->scalarNode('password')->defaultNull()->end()
                        ->scalarNode('path')->defaultNull()->end()
                        ->scalarNode('security')->defaultNull()->end()
                        ->scalarNode('auth_mode')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
