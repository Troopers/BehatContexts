<?php
/**
 * Created by PhpStorm.
 * User: charlie
 * Date: 03/01/2017
 * Time: 17:24
 */

namespace Troopers\BehatContexts\Tests\Mail;


use Alex\MailCatcher\Tests\AbstractTest;
use Troopers\BehatContexts\Component\ConfigTranslator;
use Troopers\BehatContexts\ContentValidator\StringValidator;
use Troopers\BehatContexts\DependencyInjection\Compiler\ContentValidatorChain;
use Troopers\BehatContexts\Mail\MailChecker;

class MailCheckerTest extends AbstractTest
{

    /**
     *
     */
    public function testBuild()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Mail to test')
            ->setFrom('sender@sender.sender' )
            ->setTo('to@to.to')
            ->setBody('Content')
        ;
        $this->sendMessage($message);

        $mailChecker = $this->getMailChecker();
        $this->invokeMethod($mailChecker, 'build',
            [
                [
                    'to' => '%user%',
                    'from' => 'sender@sender.sender',
                    'subject' => 'Mail to %context%',
                    'contents' => [
                        'strings' => ['Content']
                    ]
                ],
                [
                    ['user', 'to@to.to'],
                    ['context', 'test'],
                ]
            ]);
    }

    public function testBuildWithMissingTranslations()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Mail to test')
            ->setFrom('sender@sender.sender' )
            ->setTo('to@to.to')
            ->setBody('Content')
        ;
        $this->sendMessage($message);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing translations : %context%');

        $mailChecker = $this->getMailChecker();
        $this->invokeMethod($mailChecker, 'build',
            [
                [
                    'to' => '%user%',
                    'from' => 'sender@sender.sender',
                    'subject' => 'Mail to %context%',
                    'contents' => [
                        'strings' => ['Wrong Content']
                    ]
                ],
                [
                    ['user', 'to@to.to']
                ]
            ]);

    }

    public function testMissingBuildContent()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Mail to test')
            ->setFrom('sender@sender.sender' )
            ->setTo('to@to.to')
            ->setBody('Content')
        ;
        $this->sendMessage($message);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to find text "Wrong Content" in current message:');
        $mailChecker = $this->getMailChecker();
        $this->invokeMethod($mailChecker, 'build',
            [
                [
                    'to' => '%user%',
                    'from' => 'sender@sender.sender',
                    'subject' => 'Mail to %context%',
                    'contents' => [
                        'strings' => ['Wrong Content']
                    ]
                ],
                [
                    ['user', 'to@to.to'],
                    ['context', 'test']
                ]
            ]);

    }

    public function testFindMails()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Mail to test')
            ->setFrom('sender@sender.sender' )
            ->setTo('to@to.to')
            ->setBody('Content')
        ;
        $this->sendMessage($message);

        $mailChecker = $this->getMailChecker();
        $this->assertNotEmpty(
            $this->invokeMethod($mailChecker, 'findMail',
            [
                [
                    'to' => 'to@to.to',
                    'from' => 'sender@sender.sender',
                    'subject' => 'Mail to test',
                ]
            ])
        );
    }

    public function testCannotFindMails()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Mail to test')
            ->setFrom('sender@sender.sender' )
            ->setTo('to@to.to')
            ->setBody('Content')
        ;
        $this->sendMessage($message);

        $mailChecker = $this->getMailChecker();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to find a message with criterias "{"to":"to@to.to","from":"wrongsender@sender.sender","subject":"Mail to test"}"');
        $this->expectExceptionMessage('Available mails: 1');
        $this->expectExceptionMessage('{"to":"to@to.to","from":"sender@sender.sender","subject":"Mail to test"}');
        $this->invokeMethod($mailChecker, 'findMail',
        [
            [
                'to' => 'to@to.to',
                'from' => 'wrongsender@sender.sender',
                'subject' => 'Mail to test',
            ]
        ]);
    }

    /**
     * @param \Swift_Message $message
     * @param                $link
     * @param                $href
     *
     * @dataProvider swiftMessageWithLinkProvider
     */
    public function testGetLink(\Swift_Message $message, $link, $href)
    {
        $this->sendMessage($message);
        $mailChecker = $this->getMailChecker();
        $this->assertEquals(
             $href
            ,$mailChecker->getLink(
                [
                    'to' => 'to@to.to',
                    'from' => 'sender@sender.sender',
                    'subject' => 'Mail to test',
                    'contents' => []
                ], [], $link
            )
        );

    }

    public function swiftMessageWithLinkProvider()
    {
        return [

            [
                \Swift_Message::newInstance()
                    ->setSubject('Mail to test')
                    ->setFrom('sender@sender.sender' )
                    ->setTo('to@to.to')
                    ->setBody('<p>Content \n <a href="link1" id="test_link">Test Link</a></p>', 'text/html'),
                'Test Link',
                'link1'
            ],
            [
                \Swift_Message::newInstance()
                    ->setSubject('Mail to test')
                    ->setFrom('sender@sender.sender' )
                    ->setTo('to@to.to')
                    ->setBody('<p>Content \n <a href="link2" id="test_link">Test Link</a></p>', 'text/html'),
                'test_link',
                'link2'
            ],
        ];
    }

    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @return \Troopers\BehatContexts\Mail\MailChecker
     */
    private function getMailChecker()
    {
        $mailConfig = ['translation' =>['firstCharacter' => '%', 'lastCharacter'=>'%']];
        $contentValidatorChain = $this->createMock(ContentValidatorChain::class);
        $contentValidatorChain->method('getContentValidator')
            ->will(
              $this->returnValueMap(
                  [
                      [
                          'strings',  new StringValidator()
                      ]
                  ]
              )
            );
        return new MailChecker(
            new ConfigTranslator(),
            $mailConfig,
            $this->getClient(),
            $contentValidatorChain
        );
    }

    public function setUp()
    {
        $this->getClient()->purge();
    }
}
