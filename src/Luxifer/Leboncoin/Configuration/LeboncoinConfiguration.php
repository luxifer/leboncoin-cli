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
                            ->scalarNode('region')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('category')->isRequired()->cannotBeEmpty()->end() // Catégorie
                            ->scalarNode('location')->end() // Ville
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
        $nodes = $builder->root('parameters');

        $nodes
            ->children()
                ->integerNode('mre')->end() // Locations : Loyer max
                ->integerNode('ros')->end() // Locations : Pièces min
                ->integerNode('furn')->treatTrueLike(1)->treatFalseLike(2) // Locations : Meublé / Non meublé
            ->end()
        ;

        return $nodes;
    }
}
