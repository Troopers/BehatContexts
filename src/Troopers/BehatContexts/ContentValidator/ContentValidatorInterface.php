<?php

namespace Troopers\BehatContexts\ContentValidator;

interface ContentValidatorInterface {
    /**
     * @param mixed  $value
     * @param string $content
     *
     * @return mixed
     */
    public function valid($value, $content = '');

    /**
     * @param mixed $value
     *
     */
    public function supports($value);
}