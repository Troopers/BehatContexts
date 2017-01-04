<?php

namespace Troopers\BehatContexts\ContentValidator;

use DOMDocument;

class TableValidator implements ContentValidatorInterface {
    /**
     * @param mixed $value
     *
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
     */
    public function supports($value)
    {
        if (!is_array($value))
        {
            throw new ContentValidatorException(sprintf('Value given (%s) is not an array', json_encode($value)));
        } else {
            /** @var array $value */
            foreach ($value as $index => $table)
            {
                if (!is_array($table))
                {
                    throw new ContentValidatorException(sprintf('Value given (%s) on  $s row is not an array', $index+1, json_encode($table)));
                }
            }
        }
    }

    /**
     * @param array  $value
     * @param string $content
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function valid( $value = [], $content = '')
    {

        $doc = new DOMDocument();
        $doc->loadHTML($content);
        if (!$htmlArray = $doc->getElementById('array')) {
            throw new \InvalidArgumentException("Unable to find array with id \"array\"");
        }
        /**
         * @var int $lineNb
         * @var array $cells
         */
        foreach ($value as $lineNb => $cells) {
            foreach ($cells as $colNb => $cellValue) {
                $htmlValue = $htmlArray->getElementsByTagName('tr')->item($lineNb)->getElementsByTagName('td')->item($colNb)->nodeValue;
                if (false === strpos($htmlValue, $cellValue)) {
                    throw new \InvalidArgumentException(sprintf("Unable to find text \"%s\" in current cell:\n%s", $cellValue, $content));
                }
            }
        }
    }
}