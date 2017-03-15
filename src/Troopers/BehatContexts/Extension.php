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
        // Define Parameters
        $parameters = [];
        $container->addCompilerPass(new Compiler\ContentValidatorCompilerPass());
        $this->buildParameters('troopers.behatcontexts', $parameters, $config);
        foreach ($parameters as $name => $parameter) {
            $container->setParameter($name, $parameter);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.yml');

        if (isset($config['alias_entity']) && isset($config['alias_entity']['enabled'])) {
            $container->addCompilerPass(new Compiler\AliasEntityPass());
        }
        $container->addCompilerPass(new Compiler\ExtendedTableNodePass());
        if (isset($config['mails']) && isset($config['mails']['path']) && isset($config['mails']['key'])) {
            $loader->load('mail.yml');
            $container->addCompilerPass(new Compiler\MailPass());
        }
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
                ->arrayNode('mails')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')
                            ->defaultValue(null)
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('key')
                            ->defaultValue(null)
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('translation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('firstCharacter')
                                    ->defaultValue('%')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('lastCharacter')
                                    ->defaultValue('%')
                                    ->isRequired()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
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

    /**
     * @param $name
     * @param $parameters
     * @param $config
     */
    protected function buildParameters($name, &$parameters, $config)
    {
        foreach ($config as $key => $element) {
            if (is_array($element) && $this->arrayHasStringKeys($element)) {
                $this->buildParameters(sprintf('%s.%s', $name, $key), $parameters, $element);
            }
            $parameters[sprintf('%s.%s', $name, $key)] = $element;
        }
    }

    /**
     * @param array $array
     *
     * @return bool
     */
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
