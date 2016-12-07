<?php

namespace Troopers\BehatContexts;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Knp\FriendlyContexts\DependencyInjection\Compiler;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension implements ExtensionInterface
{
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/services'));
        /*$loader->load('core.yml');
        $loader->load('fakers.yml');
        $loader->load('guessers.yml');
        $loader->load('builder.yml');

        $container->setParameter('friendly.parameters', $config);

        $container->addCompilerPass(new Compiler\ConfigPass);
        $container->addCompilerPass(new Compiler\FormatGuesserPass);
        $container->addCompilerPass(new Compiler\FakerProviderPass);
        $container->addCompilerPass(new Compiler\ApiUrlPass);
        $container->addCompilerPass(new Compiler\KernelPass);*/
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('extended_entity')
                    ->canBeEnabled()
                ->end()
            ->end()
        ;
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'friendly';
    }

    protected function buildParameters($name, &$parameters, $config)
    {
        foreach ($config as $key => $element) {
            if (is_array($element) && $this->arrayHasStringKeys($element)) {
                $this->buildParameters(sprintf('%s.%s', $name, $key), $parameters, $element);
            }
            $parameters[sprintf('%s.%s', $name, $key)] = $element;
        }
    }

    protected function arrayHasStringKeys(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_string($key)) {

                return true;
            }
        }

        return false;
    }
}
