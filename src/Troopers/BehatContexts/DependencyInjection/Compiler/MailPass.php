<?php

namespace Troopers\BehatContexts\DependencyInjection\Compiler;

use Behat\Testwork\ServiceContainer\Exception\ExtensionInitializationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Pass to permit to alias entity.
 */
class MailPass implements CompilerPassInterface
{
    /**
     * use TroopersEntityHydrator to replace friendly.entity.hydrator.
     *
     * @param ContainerBuilder $container
     *
     * @throws ExtensionInitializationException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     * @throws ServiceCircularReferenceException
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('troopers.behatcontexts.mail.parser')) {
            $mailParser = $container->get('troopers.behatcontexts.mail.parser');
            $mailParser->loadMails();
        }
    }
}
