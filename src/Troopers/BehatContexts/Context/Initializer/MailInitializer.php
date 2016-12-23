<?php

namespace Troopers\BehatContexts\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\Context\Context as ContextInterface;
use Troopers\BehatContexts\BehatContexts\Context\MailContext;
use Troopers\BehatContexts\Collection\MailCollection;

class MailInitializer implements ContextInitializer
{
    protected $mailCollection;

    /**
     * MailInitializer constructor.
     *
     * @param \Troopers\BehatContexts\Collection\MailCollection $mailCollection
     */
    public function __construct(MailCollection $mailCollection)
    {
        $this->mailCollection = $mailCollection;
    }

    /**
     * @param $context
     *
     * @return bool
     */
    public function supports($context)
    {
        return $context instanceof MailContext;
    }

    /**
     * @param \Behat\Behat\Context\Context $context
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof MailContext) {

            $context->initialize($this->mailCollection);
        }
    }
}
