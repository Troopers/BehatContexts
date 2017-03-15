<?php

namespace Troopers\BehatContexts\Tests\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Troopers\BehatContexts\Extension;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Extension $extension */
    private $extension = null;
    /** @var ContainerBuilder $containerBuilder */
    private $containerBuilder = null;

    public function setUp()
    {
        $this->extension = new Extension();
        $this->containerBuilder = new ContainerBuilder();

        $config = [];

        $this->extension->load($this->containerBuilder, $config);
    }

    /**
     * @dataProvider configProvider
     *
     * @param string $node  Array key from parametersProvider
     * @param string $value Array value from parametersProvider
     */
    public function testLoad($node, $value)
    {
        $name = 'troopers.behatcontexts.'.$node;

        $this->assertTrue(
            $this->containerBuilder->hasParameter($name),
            $node.' parameter is not defined.'
        );
        $this->assertSame(
            $value,
            $this->containerBuilder->getParameter($name)
        );
    }

    public function configProvider()
    {
        return [
            ['alias_entity.enabled', false],
            ['mails.path', ''],
            ['mails.key', ''],
            ['mails.translation.firstCharacter', '%'],
            ['mails.translation.lastCharacter', '%'],
        ];
    }
}
