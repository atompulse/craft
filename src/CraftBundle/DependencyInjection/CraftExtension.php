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
        $configValue = $config['security']['authorization_registry'];
        $container->setAlias('craft.security.authorization.authorization_registry', $configValue);

        // add alias for UserRegistryInterface
        $configValue = $config['security']['user_registry'];
        $container->setAlias('craft.security.authorization.user_registry', $configValue);

        // add alias for UserDataInterface
        $configValue = $config['security']['user_data'];
        $container->setParameter('craft.security.user_data', $configValue);

        // add alias for ActionArgumentBuilderInterface
        $configValue = $config['http']['action_argument_builder'];
        $container->setAlias('craft.http.controller.action_argument_builder', $configValue);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');


    }
}
