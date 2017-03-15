<?php

namespace Troopers\BehatContexts\Parser\Mail;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Troopers\BehatContexts\Collection\MailCollection;
use Troopers\BehatContexts\Component\ConfigReader;

/**
 * Class MailParser.
 */
class MailParser
{
    private $reader;
    private $mailsConfig;
    private $mailCollection;

    /**
     * MailParser constructor.
     *
     * @param ConfigReader                                      $reader
     * @param \Troopers\BehatContexts\Collection\MailCollection $mailCollection
     * @param array                                             $mailsConfig
     */
    public function __construct(ConfigReader $reader, MailCollection $mailCollection, array $mailsConfig)
    {
        $this->reader = $reader;
        $this->mailsConfig = $mailsConfig;
        $this->mailCollection = $mailCollection;
    }

    public function loadMails()
    {
        foreach ($this->mailsConfig as $mailConfig) {
            $config = $this->reader->load($mailConfig['path'], $mailConfig['key']);
            foreach ($config as $eventName => $emailConfig) {
                $this->validMailConfig($eventName, $emailConfig);
                $this->mailCollection->set($eventName, $emailConfig);
            }
        }
    }

    /**
     * @param $event
     * @param $config
     */
    private function validMailConfig($event, $config)
    {
        $exceptions = [];
        if (!isset($config['to'])) {
            $exceptions[] = 'Missing configuration for "to"';
        } elseif (!$config['to']) {
            $exceptions[] = 'Value not found for "to"';
        }
        if (!isset($config['from'])) {
            $exceptions[] = 'Missing configuration for "from"';
        } elseif (!$config['from']) {
            $exceptions[] = 'Value not found for "from"';
        }
        if (!isset($config['subject'])) {
            $exceptions[] = 'Missing configuration for "subject"';
        } elseif (!$config['subject']) {
            $exceptions[] = 'Value not found for "subject"';
        }
        if (count($exceptions) > 0) {
            throw new InvalidConfigurationException(sprintf('Invalid for event Configuration "%s" : %s', $event, implode(', ', $exceptions)));
        }
    }
}
