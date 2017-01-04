<?php

namespace Troopers\BehatContexts\DependencyInjection\Compiler;
use Troopers\BehatContexts\ContentValidator\ContentValidatorException;
use Troopers\BehatContexts\ContentValidator\ContentValidatorInterface;

/**
 * Class ContentValidatorChain
 *
 * @package Troopers\BehatContexts\DependencyInjection\Compiler
 */
class ContentValidatorChain {
    private $contentValidators;

    public function __construct()
    {
        $this->contentValidators = [];
    }

    /**
     * @param $key
     *
     * @return ContentValidatorInterface
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
     */
    public function getContentValidator($key)
    {
        if(!array_key_exists($key, $this->contentValidators))
        {
            throw new ContentValidatorException(sprintf('Undefined ContentValidator for %s key', $key));
        }
        return $this->contentValidators[$key];
    }

    /**
     * @param \Troopers\BehatContexts\ContentValidator\ContentValidatorInterface $contentValidator
     * @param                                                                    $key
     *
     */
    public function addContentValidator(ContentValidatorInterface $contentValidator, $key)
    {
        $this->contentValidators[$key] = $contentValidator;
    }

}