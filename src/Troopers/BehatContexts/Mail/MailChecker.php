<?php
namespace Troopers\BehatContexts\Mail;
use Alex\MailCatcher\Client;
use Alex\MailCatcher\Message;
use Behat\Mink\Selector\NamedSelector;
use Symfony\Component\DomCrawler\Crawler;
use Troopers\BehatContexts\Component\ConfigTranslator;


/**
 * Class MailChecker
 *
 * @package Troopers\BehatContexts\Mail
 */
class MailChecker {

    private $configTranslator;
    private $mailConfig;
    private $mailcatcherClient;

    /**
     * MailChecker constructor.
     *
     * @param \Troopers\BehatContexts\Component\ConfigTranslator $configTranslator
     */
    public function __construct(ConfigTranslator $configTranslator, $mailConfig, Client $mailcatcherClient)
    {
        $this->configTranslator = $configTranslator;
        $this->mailConfig = $mailConfig;
        $this->mailcatcherClient = $mailcatcherClient;
    }

    /**
     * @param array $mail
     * @param array $values
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function check(array $mail = array(), array $values = array())
    {
        $this->build($mail, values);
        if(isset($mail['CCI']))
        {
            $this->build(array_merge($mail, ['to' => $mail['CCI']]), $values);
        }
    }

    /**
     * @param $mail
     * @param $values
     *
     * @return mixed
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function build(array $mail, array $values)
    {
        $this->configTranslator->rebuildTranslationKeys(
            $values,
            $this->mailConfig['translation']['firstCharacter'],
            $this->mailConfig['translation']['lastCharacter']
        );
        $mailToTest = $this->configTranslator->translate($mail, $values);
        $missingTranslations = $this->configTranslator->getMissingTranslations(
            $mailToTest,
            $this->mailConfig['translation']['firstCharacter'],
            $this->mailConfig['translation']['lastCharacter']
        );

        if (count($missingTranslations) > 0)
        {
            throw new \InvalidArgumentException(
                'Missing translations : '.implode(', ', $missingTranslations)
            );

        }
        $message = $this->findMail([
            'to' => $mailToTest['to'],
            'from' => $mailToTest['from'],
            'subject' => $mailToTest['subject']
        ]);
        $content = $this->getContent($message);
        //test contents
        foreach ($mailToTest['contents'] as $text) {
            if (false === strpos($content, $text)) {
                throw new \InvalidArgumentException(sprintf("Unable to find text \"%s\" in current message:\n%s", $text, $message->getContent()));
            }
        }


        return $content;
    }

    /**
     * @param array $criterias
     *
     * @return \Alex\MailCatcher\Message|null
     * @throws \InvalidArgumentException
     */
    private function findMail(array $criterias){

        //get mail with "to", "from" and "subject"

        $message = $this->mailcatcherClient->searchOne($criterias);

        // if not found throw exception
        if (null === $message) {
            $currentMails = [];
            /** @var \Alex\MailCatcher\Message $message */
            foreach ($this->mailcatcherClient->search() as $message) {
                $recipients = '';
                /** @var \Alex\MailCatcher\Person $recipient */
                foreach ($message->getRecipients() as $recipient) {
                    $recipients .= $recipient->getEmail().' ';
                }
                $currentMails[] = json_encode([
                    'to' => trim($recipients),
                    'from' => $message->getSender()->getEmail(),
                    'subject' => $message->getSubject(),
                ]);
            }
            $exceptions = array_merge([
                sprintf('Unable to find a message with criterias "%s"', json_encode($criterias)),
                sprintf('Available mails: %s', count($currentMails)),
            ], $currentMails);
            throw new \InvalidArgumentException(implode("\n", $exceptions));
        }
        return $message;
    }

    /**
     * @param \Alex\MailCatcher\Message $message
     *
     * @return string
     */
    private function getContent(Message $message)
    {
        // test message
        if (!$message->isMultipart()) {
            $content = $message->getContent();
        } elseif ($message->hasPart('text/html')) {
            $crawler = new Crawler($message);
            $content = $crawler->filter('body')->text();
        } elseif ($message->hasPart('text/plain')) {
            $content = $message->getPart('text/plain')->getContent();
        } else {
            throw new \RuntimeException(sprintf('Unable to read mail'));
        }

        if (true === strpos($content, '</')) {
            throw new \InvalidArgumentException('Found mark up on email');
        }
        return $content;
    }

    public function getLink(array $mail = array(), array $values = array(), $link)
    {
        $selector = new NamedSelector();
        $xpath = $selector->translateToXPath(['link', $link]);
        $content = $this->build($mail, $values);
        $crawler = new Crawler($content);
        $mailLink = $crawler->filterXPath($xpath);
        return $mailLink->attr('href');
    }
}