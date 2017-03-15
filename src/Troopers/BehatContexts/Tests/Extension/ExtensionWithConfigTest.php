<?php

namespace Troopers\BehatContexts\Tests\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Troopers\BehatContexts\Extension;

class ExtensionWithConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Extension $extension */
    private $extension = null;
    /** @var ContainerBuilder $containerBuilder */
    private $containerBuilder = null;

    public function setUp()
    {
        $this->extension = new Extension();
        $this->containerBuilder = new ContainerBuilder();

        $config = [
            'alias_entity' => ['enabled' => true],
            'mails'        => [
                'path'        => 'path/to/emailConfig/directory',
                'key'         => 'mailsconfigkey',
                'translation' => [
                    'firstCharacter' => 'f',
                    'lastCharacter'  => 'l',
                ],
            ],
        ];

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
            ['alias_entity', ['enabled' => true]],
            ['alias_entity.enabled', true],
            ['mails.path', 'path/to/emailConfig/directory'],
            ['mails.key', 'mailsconfigkey'],
            ['mails.translation.firstCharacter', 'f'],
            ['mails.translation.lastCharacter', 'l'],
        ];
    }
}
