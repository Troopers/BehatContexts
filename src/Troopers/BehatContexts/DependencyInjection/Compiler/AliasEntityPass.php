<?php

namespace Troopers\BehatContexts\DependencyInjection\Compiler;

use Behat\Testwork\ServiceContainer\Exception\ExtensionInitializationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Troopers\BehatContexts\Doctrine\EntityHydrator as TroopersEntityHydrator;

/**
 * Pass to permit to alias entity.
 */
class AliasEntityPass implements CompilerPassInterface
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
        if ($container->hasDefinition('friendly.entity.hydrator')) {
            $definition = $container->getDefinition('friendly.entity.hydrator');
            $definition->setClass(TroopersEntityHydrator::class);
        } else {
            throw new ExtensionInitializationException('Missing extension Knp\FriendlyContexts\Extension', 'Knp\FriendlyContexts\Extension');
        }
    }
}
