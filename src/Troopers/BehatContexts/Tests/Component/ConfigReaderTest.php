<?php

namespace Troopers\BehatContexts\Tests\Component;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Troopers\BehatContexts\Component\ConfigReader;

/**
 * Class ConfigReaderTest.
 */
class ConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $driver;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $configDirectory;

    private $directoryName = 'config_test';

    /**
     * Set up vfs stream driver.
     */
    protected function setUp()
    {
        $this->driver = vfsStream::setup();
        $this->configDirectory = vfsStream::newDirectory($this->directoryName);
        $this->driver->addChild($this->configDirectory);
    }

    /**
     * Close driver.
     */
    protected function tearDown()
    {
        $this->driver = null;
    }

    /**
     * @param $config
     * @param $expected
     * @dataProvider oneFileConfigProvider
     */
    public function testReadOneFileConfig($config, $expected)
    {
        $this->generateConfigFile($config);
        $configReader = new ConfigReader($this->driver->url());
        $parsedConfig = $configReader->load($this->directoryName, 'test_key');
        $this->assertEquals($parsedConfig, $expected);
    }

    public function testReadMultiplesFilesConfig()
    {
        $configs = [
            [
                'test_key' => [
                    'arg1' => [
                        'test' => 'value',
                    ],
                ],
            ],
            [
                'test_key' => [
                    'arg2' => [
                        'test2' => 'value2',
                    ],
                ],
            ],
        ];
        foreach ($configs as $config) {
            $this->generateConfigFile($config);
        }
        $configReader = new ConfigReader($this->driver->url());
        $parsedConfig = $configReader->load($this->directoryName, 'test_key');
        $this->assertEquals($parsedConfig,
            [
                'arg1' => [
                    'test' => 'value',
                ],
                'arg2' => [
                    'test2' => 'value2',
                ],
            ]
        );
    }

    public function testNoConfigFileFound()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(
            'No configuration found'
        );
        $configReader = new ConfigReader($this->driver->url());
        $configReader->load($this->directoryName, 'test_key');
    }

    public function testInvalidConfigFile()
    {
        $configs = [
            [
                'test_key' => [
                    'arg1' => [
                        'test' => 'value',
                    ],
                ],
            ],
            [
                'test_key' => [
                    'arg1' => [
                        'test2' => 'value2',
                    ],
                ],
            ],
        ];
        foreach ($configs as $config) {
            $this->generateConfigFile($config);
        }
        $configReader = new ConfigReader($this->driver->url());
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageRegExp(
            '/Duplicate key "arg1" for configurations files located in (\S*).yml$/'
        );
        $configReader->load($this->directoryName, 'test_key');
    }

    public function testNoConfigDefinedInFile()
    {
        $configs = [
            [
                'test_key' => [
                    'arg1' => [
                        'test' => 'value',
                    ],
                ],
            ],
        ];
        foreach ($configs as $config) {
            $this->generateConfigFile($config);
        }
        $configReader = new ConfigReader($this->driver->url());

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageRegExp(
            '/The file located in (\S*).yml has no config value "wrong_key"$/'
        );
        $configReader->load($this->directoryName, 'wrong_key');
    }

    /**
     * @param null        $data
     * @param null|string $filename
     */
    private function generateConfigFile($data = null, $filename = '')
    {
        if ($filename === '') {
            $filename = md5(microtime()).'.yml';
        }
        if (!$this->driver->hasChild($filename)) {
            $file = vfsStream::newFile($filename);
            $file->setContent(Yaml::dump($data));
            $this->configDirectory->addChild($file);
        }
    }

    public function oneFileConfigProvider()
    {
        return [
            [
                [
                    'test_key' => [
                        'arg1' => [
                            'test' => 'value',
                        ],
                    ],
                ],
                [
                    'arg1' => [
                        'test' => 'value',
                    ],
                ],
            ],
        ];
    }
}
