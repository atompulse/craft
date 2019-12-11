<?php

namespace CraftBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class ApiExtension
 * @package Atompulse\ApiBundle\DependencyInjection
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class CraftExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('craft.security.key', $config['security']['key']);

        // add alias for AuthorizationRegistryInterface
        $authorizationRegistry = $config['security']['authorization_registry'];
        $container->setAlias('Craft\Security\Authorization\AuthorizationRegistryInterface', $authorizationRegistry);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');


    }
}
