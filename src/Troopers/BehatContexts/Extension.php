<?php

namespace Troopers\BehatContexts;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Troopers\BehatContexts\DependencyInjection\Compiler;

/**
 * Class Extension.
 */
class Extension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws \Exception
     */
    public function load(ContainerBuilder $container, array $config)
    {
        if (isset($config['alias_entity']) && isset($config['alias_entity']['enabled'])) {
            $container->addCompilerPass(new Compiler\AliasEntityPass());
        }
        $container->addCompilerPass(new Compiler\ExtendedTableNodePass());
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('alias_entity')
                    ->canBeEnabled()
                ->end()
            ->end();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return 'troopers_behat';
    }
}
