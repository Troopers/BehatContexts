<?php

namespace Troopers\BehatContexts\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ContentValidatorCompilerPass.
 */
class ContentValidatorCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('troopers.behatcontexts.content_validator.chain')) {
            return;
        }
        $definition = $container->findDefinition('troopers.behatcontexts.content_validator.chain');

        $taggedServices = $container->findTaggedServiceIds('troopers.behatcontexts.content_validator');

        foreach ($taggedServices as $id => $tags) {
            /** @var array $tags */
            foreach ($tags as $attributes) {
                if (!array_key_exists('contentType', $attributes)) {
                    throw new InvalidConfigurationException(sprintf('Missing contentType attribute for %s', $id));
                }
                $definition->addMethodCall('addContentValidator', [
                    new Reference($id),
                    $attributes['contentType'],
                ]);
            }
        }
    }
}
