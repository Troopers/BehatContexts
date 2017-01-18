<?php

namespace Troopers\BehatContexts\Component;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class ConfigReader
{
    private $rootDir;

    /**
     * ConfigReader constructor.
     *
     * @param $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param $path
     * @param $key
     *
     * @throws \RuntimeException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     *
     * @return array
     */
    public function load($path, $key)
    {
        $finder = new Finder();
        $finder->files()->in($this->rootDir.'/'.$path);
        $result = [];
        if (!$finder->count()) {
            throw new FileNotFoundException('No configuration found');
        }
        // read yaml config files
        foreach ($finder as $configFile) {
            // check config
            /** @var array $config */
            $config = Yaml::parse($configFile->getContents());
            if (!isset($config[$key])) {
                throw new InvalidConfigurationException(
                    'The file located in '.$configFile.' has no config value "'.$key.'"'
                );
            }
            foreach ($config[$key] as $index => $value) {
                if (array_key_exists($index, $result)) {
                    throw new InvalidConfigurationException(sprintf('Duplicate key "%s" for configurations files located in %s', $index, $configFile));
                }
                if ($value !== null) {
                    $result[$index] = $value;
                }
            }
        }

        return $result;
    }
}
