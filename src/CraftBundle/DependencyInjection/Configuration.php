<?php

namespace CraftBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Configuration
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('craft');

        // load bundle defaults that can be overwritten by the application
        $this->defaults = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/config.defaults.yaml'));

        $treeBuilder->getRootNode()->append($this->addSecurityConfiguration());
        $treeBuilder->getRootNode()->append($this->addHttpConfiguration());

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function addSecurityConfiguration(): ArrayNodeDefinition
    {
        $node = (new TreeBuilder('security'))->getRootNode();

        $node
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
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    protected function addHttpConfiguration(): ArrayNodeDefinition
    {
        $node = (new TreeBuilder('http'))->getRootNode();

        $node
            ->isRequired()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('action_argument_builder')
            ->cannotBeEmpty()
            ->defaultValue($this->defaults['http']['action_argument_builder'])
            ->end()
            ->end();

        return $node;
    }
}