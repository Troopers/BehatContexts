<?php

namespace Troopers\BehatContexts\Utils;

use Knp\FriendlyContexts\Utils\TextFormater as BaseTextFormater;

class TextFormater extends BaseTextFormater
{
    /**
     * @param $list
     * @param array  $delimiters
     * @param string $parser
     * @param string $link
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    public function listToArray($list, $delimiters = [', ', ' and '], $parser = '#||#', $link = ':')
    {
        $list = str_replace('"', '', $list);
        foreach ($delimiters as $delimiter) {
            $list = str_replace($delimiter, $parser, $list);
        }
        if (!is_string($list)) {
            throw new \InvalidArgumentException(sprintf('The list must be a string, not a "%s" element', gettype($list)));
        }
        $parts = explode($parser, $list);
        if (false !== strpos($list, $link)) {
            $parts = $this->orderKeyValueTable($parts, $link);
        }
        $parts = array_map('trim', $parts);
        $parts = array_filter($parts, 'strlen');

        return $parts;
    }

    /**
     * @param array $parts
     * @param $link
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    protected function orderKeyValueTable(array $parts, $link)
    {
        $keyValueTable = [];
        foreach ($parts as $part) {
            $part = explode($link, $part);
            if (count($part) % 2 === 0) {
                $iiMax = count($part);
                for ($ii = 0; $ii < $iiMax; $ii += 2) {
                    $keyValueTable[trim($part[$ii])] = $part[$ii + 1];
                }
            } else {
                throw new \UnexpectedValueException('Key without value in list');
            }
        }

        return $keyValueTable;
    }
}
