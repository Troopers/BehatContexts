<?php

namespace Troopers\BehatContexts\Tests\Component;

use Troopers\BehatContexts\Component\ConfigTranslator;

/**
 * Class ConfigTranslatorTest
 *
 * @package Troopers\BehatContexts\Tests\Component
 */
class ConfigTranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $config
     * @param $parameters
     * @param $expected
     * @dataProvider translationsProvider
     */
    public function testTranslate($config, $parameters, $expected)
    {
        $configTranslator = new ConfigTranslator();
        $translation = $configTranslator->translate($config, $parameters);

        $this->assertEquals($expected, $translation);
    }

    public function testRebuildTranslationKeys()
    {
        $configTranslator = new ConfigTranslator();
        $parameters = [
            ['key1', 'value1'],
            ['key2', 'value2']
        ];
        $configTranslator->rebuildTranslationKeys($parameters, '%', '%');
        $this->assertEquals(
            [
                '%key1%' => 'value1',
                '%key2%' => 'value2'
            ]
            , $parameters);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRebuildTranslationKeys()
    {
        $configTranslator = new ConfigTranslator();
        $parameters = [
            ['key1', 'value1'],
            ['key2']
        ];
        $configTranslator->rebuildTranslationKeys($parameters, '%', '%');
    }

    public function testGetMissingTranslations()
    {
        $configTranslator = new ConfigTranslator();
        $missingTranslations = $configTranslator->getMissingTranslations(
            [
                'test' => 'va%lue',
                'test2' => [
                    'test3' => 'value2'
                ]
            ],
            '%',
            '%'
        );
        $this->assertEquals([], $missingTranslations);
        $missingTranslations = $configTranslator->getMissingTranslations(
            [
                'test' => '%val%ue',
                'test2' => [
                    'test3' => '%val%ue2'
                ],
                'test4' => 'bla%bla%bla'
            ],
            '%',
            '%'
        );
        $this->assertEquals(['%val%', '%bla%'], $missingTranslations);
    }

    public function translationsProvider()
    {
        return [
            ['simple translation' =>
                [
                    'index' => [
                        'test' => '%value%'
                    ]
                ],
                ['%value%' => 'phpunit'],
                [
                    'index' => [
                        'test' => 'phpunit'
                    ]
                ]
            ],
            ['recursive translation' =>
                [
                    'index' => [
                        'test' => [
                            'test_test' =>  '%value%'
                        ]
                    ]
                ],
                ['%value%' => 'phpunit'],
                [
                    'index' => [
                        'test' => [
                            'test_test' =>  'phpunit'
                        ]
                    ]
                ],
            ],
            ['multiple translation' =>
                [
                    'index' => [
                        'test' => '%value%',
                        'test2' => '%value2%'
                    ]
                ],
                ['%value%' => 'first', '%value2%' => 'second'],
                [
                    'index' => [
                        'test' => 'first',
                        'test2' => 'second'
                    ]
                ]
            ],
            ['mixed translation' =>
                [
                    'index' => [
                        'test' => 'tetsu  jssfdsf o,%value% sfcsibn49451'
                    ]
                ],
                ['%value%' => 'phpunit'],
                [
                    'index' => [
                        'test' => 'tetsu  jssfdsf o,phpunit sfcsibn49451'
                    ]
                ]
            ],


        ];
    }
}
