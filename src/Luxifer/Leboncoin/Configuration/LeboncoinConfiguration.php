<?php
namespace Luxifer\Leboncoin\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class LeboncoinConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('leboncoin');

        $rootNode
            ->children()
                ->scalarNode('url')->defaultValue('http://www.leboncoin.fr')->end()
                ->arrayNode('criterias')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('q')->defaultNull()->end() // Recherche
                            ->scalarNode('region')->isRequired()->cannotBeEmpty()->end() // Région
                            ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('category')->defaultValue('annonces')->end() // Catégorie
                            ->scalarNode('location')->defaultNull()->end() // Ville
                            ->scalarNode('department')->end() // Département
                            ->scalarNode('f')
                                ->defaultValue('a')
                                ->validate()
                                ->ifNotInArray(array('a', 'p', 'c'))
                                    ->thenInvalid('Invalid type "%s"')
                                ->end()
                            ->end() // Type (tout/particulier/pro)
                            ->append($this->addParametersNode())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    protected function addParametersNode()
    {
        $builder = new TreeBuilder();
        $nodes = $builder->root('filters');

        $nodes
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('value')->end()
                ->end()
            ->end()
        ;

        return $nodes;
    }
}
