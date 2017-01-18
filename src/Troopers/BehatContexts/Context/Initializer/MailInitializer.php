<?php

namespace Troopers\BehatContexts\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Troopers\BehatContexts\Collection\MailCollection;
use Troopers\BehatContexts\Context\MailContext;
use Troopers\BehatContexts\Mail\MailChecker;

class MailInitializer implements ContextInitializer
{
    protected $mailCollection;
    protected $mailChecker;

    /**
     * MailInitializer constructor.
     *
     * @param \Troopers\BehatContexts\Collection\MailCollection $mailCollection
     * @param \Troopers\BehatContexts\Mail\MailChecker          $mailChecker
     */
    public function __construct(MailCollection $mailCollection, MailChecker $mailChecker)
    {
        $this->mailCollection = $mailCollection;
        $this->mailChecker = $mailChecker;
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
            $context->initialize($this->mailCollection, $this->mailChecker);
        }
    }
}
