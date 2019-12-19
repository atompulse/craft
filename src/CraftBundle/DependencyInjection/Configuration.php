<?php

namespace CraftBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package CraftBundle\DependencyInjection
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('craft');

        $this->addSecurityConfiguration($treeBuilder->getRootNode());

        $this->addHttpConfiguration($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    protected function addSecurityConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('security')
                    ->isRequired()
                    ->children()
                        ->scalarNode('key')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()
                        ->scalarNode('user_registry')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()
                        ->scalarNode('user_data')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()
                        ->scalarNode('authorization_registry')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()
                        ->scalarNode('authentication_disabled')
                            ->defaultFalse()
                            ->info('Disable the authentication system')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addHttpConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('http')
                    ->isRequired()
                    ->children()
                        ->scalarNode('action_argument_builder')
                            ->cannotBeEmpty()
                            ->isRequired()
                        ->end()

                    ->end()
                ->end()
            ->end();
    }
}