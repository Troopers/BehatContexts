<?php
namespace Troopers\BehatContexts\Mail;
use Alex\MailCatcher\Client;
use Alex\MailCatcher\Message;
use Behat\Mink\Selector\NamedSelector;
use Symfony\Component\DomCrawler\Crawler;
use Troopers\BehatContexts\Component\ConfigTranslator;
use Troopers\BehatContexts\ContentValidator\ContentValidatorInterface;
use Troopers\BehatContexts\DependencyInjection\Compiler\ContentValidatorChain;


/**
 * Class MailChecker
 *
 * @package Troopers\BehatContexts\Mail
 */
class MailChecker {

    private $configTranslator;
    private $mailConfig;
    private $mailcatcherClient;
    private $contentValidatorChain;

    /**
     * MailChecker constructor.
     *
     * @param \Troopers\BehatContexts\Component\ConfigTranslator                         $configTranslator
     * @param                                                                            $mailConfig
     * @param \Alex\MailCatcher\Client                                                   $mailcatcherClient
     * @param \Troopers\BehatContexts\DependencyInjection\Compiler\ContentValidatorChain $contentValidatorChain
     */
    public function __construct(ConfigTranslator $configTranslator, $mailConfig, Client $mailcatcherClient, ContentValidatorChain $contentValidatorChain)
    {
        $this->configTranslator = $configTranslator;
        $this->mailConfig = $mailConfig;
        $this->mailcatcherClient = $mailcatcherClient;
        $this->contentValidatorChain = $contentValidatorChain;
    }

    /**
     * @param array $mail
     * @param array $values
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
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
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
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
        if (isset($mailToTest['contents']) && is_array($mailToTest['contents']) && count($mailToTest['contents']) > 0)
        {
            /**
             * @var string $contentValidatorKey
             * @var array  $contentsToTest
             */
            foreach ($mailToTest['contents'] as $contentValidatorKey => $contentsToTest)
            {
                /** @var ContentValidatorInterface $contentValidator */
                $contentValidator = $this->contentValidatorChain->getContentValidator($contentValidatorKey);

                foreach ($contentsToTest as $value)
                {
                    $contentValidator->supports($value);
                    $contentValidator->valid($value, $content);
                }
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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
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