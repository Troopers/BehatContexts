<?php

namespace Troopers\BehatContexts\DependencyInjection\Compiler;

use Behat\Testwork\ServiceContainer\Exception\ExtensionInitializationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Troopers\BehatContexts\Utils\TextFormater;

/**
 * Pass to permit to alias entity.
 */
class ExtendedTableNodePass implements CompilerPassInterface
{
    /**
     * use TroopersEntityHydrator to replace friendly.entity.hydrator.
     *
     * @param ContainerBuilder $container
     *
     * @throws ExtensionInitializationException
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('friendly.text.formater')) {
            $definition = $container->getDefinition('friendly.text.formater');
            $definition->setClass(TextFormater::class);
        } else {
            throw new ExtensionInitializationException('Missing extension Knp\FriendlyContexts\Extension', 'Knp\FriendlyContexts\Extension');
        }
    }
}
