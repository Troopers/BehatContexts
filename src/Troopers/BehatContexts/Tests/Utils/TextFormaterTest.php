<?php

namespace Troopers\BehatContexts\Tests\Utils;


use Troopers\BehatContexts\Utils\TextFormater;

class TextFormaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $list
     *
     * @dataProvider listToArrayProvider
     */
    public function testListToArray($list, $expected)
    {
        $textFormater = new TextFormater();
        $actual = $textFormater->listToArray($list);
        $this->assertEquals($expected, $actual);
    }
    public function listToArrayProvider()
    {
        return [
            [
                'test, test2',
                [
                    'test',
                    'test2'
                ]
            ],
            [
                'test : value, test2: value2',
                [
                    'test' => 'value',
                    'test2' => 'value2'
                ]
            ]
        ];
    }
    public function testInvalidListToArray()
    {
        $textFormater = new TextFormater();
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'Key without value in list'
        );
        $textFormater->listToArray('test : value, test2');
    }
}
