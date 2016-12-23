<?php
namespace Troopers\BehatContexts\Component;

/**
 * Class ConfigTranslator
 *
 * @package Troopers\BehatContexts\Component
 */
class ConfigTranslator {
    /**
     * @param array $config
     *
     * @return array
     */
    public function getMissingTranslations(array $config = array(), $firstCharacter, $lastCharacter)
    {

        $arguments = [];
        //find string that need to be replaced
        foreach ($config as $property) {
            if (is_array($property)) {
                $arguments = array_merge($arguments, $this->getMissingTranslations($property, $firstCharacter, $lastCharacter));
            } else {
                preg_match_all('/\\'.$firstCharacter.'(\w*)\\'.$lastCharacter.'/', $property, $match);
                foreach ($match[0] as $argument) {
                    $arguments[] = $argument;
                }
            }
        }

        return array_values(array_unique($arguments));
    }

    /**
     * @param array $config
     * @param array $parameters
     *
     * @return array
     */
    public function translate(array $config = array(), array $parameters = array())
    {
        $keys = array_keys($parameters);
        $values = array_values($parameters);
        //rebuild an array of string and replace parameters keys by parameters values
        foreach ($config as $key => $property) {
            if (is_array($property)) {
                $config[$key] = $this->translate($property, $parameters);
            } else {
                $config[$key] = str_replace($keys, $values, $property);
            }
        }

        return $config;
    }

    /**
     * @param array $parameters         ['translationKey' => $translationValue]
     * @param       $firstCharacter
     * @param       $lastCharacter
     * @throws \InvalidArgumentException
     */
    public function rebuildTranslationKeys(array &$parameters, $firstCharacter, $lastCharacter)
    {
        $newParameters = [];
        foreach ($parameters as $parameter) {
            if(!is_array($parameter) || count($parameter) !== 2)
            {
                throw new \InvalidArgumentException('Parameters given for translation does not match with [\'translationKey\' => $translationValue]');
            }
            $newParameters[$firstCharacter.$parameter[0].$lastCharacter] = $parameter[1];
        }
        $parameters = $newParameters;
    }
}