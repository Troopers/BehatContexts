<?php

namespace Troopers\BehatContexts\ContentValidator;

class StringValidator implements ContentValidatorInterface {
    /**
     * @param mixed $value
     *
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
     */
    public function supports($value)
    {
        if (!is_string($value))
        {
            throw new ContentValidatorException(sprintf('Value given (%s) is not a string', json_encode($value)));
        }
    }

    /**
     * @param string $value
     * @param string $content
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function valid($value, $content = '')
    {
        if (false === strpos($content, $value)) {
            throw new \InvalidArgumentException(sprintf("Unable to find text \"%s\" in current message:\n%s", $value, $content));
        }
    }
}