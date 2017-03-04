<?php

namespace Troopers\BehatContexts\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;

/**
 * This trait implements spin methods for ajax navigation.
 *
 * @link http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html
 */
trait SpinMinkContextTrait
{
    use KernelDictionary;

    /** @var MinkContext */
    protected $minkContext;

    /**
     * Spin function to avoid Selenium fails.
     *
     * @param callable         $lambda
     * @param null             $data
     * @param int              $delay         wait 0.1 sec between each spin
     * @param int              $maxIterations maximum spin iterations, for ex 100*0.1s = 10 sec timeout
     * @param null|MinkContext $context
     *
     * @throws \Exception
     *
     * @return bool
     */
    protected function spin(callable $lambda, $maxIterations = 100, $delay = 100000, $context = null)
    {
        if (null === $context) {
            if ($this->minkContext instanceof MinkContext) {
                $context = $this->minkContext;
            } elseif ($this instanceof MinkContext) {
                $context = $this;
            } else {
                throw new \Exception(
                    'No MinkContext found in the Context using this AjaxSpinContextTrait.'
                );
            }
        }

        $e = new \Exception();
        for ($i = 0; $i < $maxIterations; $i++) {
            try {
                if ($lambda($context)) {
                    return true;
                }
            } catch (\Exception $e) {
                usleep($delay);
            }
        }

        throw $e;
    }

    /**
     * {@inheritdoc}
     *
     * @param $page
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     */
    public function assertPageAddress($page)
    {
        $this->spin(function ($context) use ($page) {
            /* @var MinkContext $context */
            $context->assertSession()->addressEquals($this->locatePath($page));

            return true;
        });
    }

    /**
     * {@inheritdoc}
     *
     * @Then /^(?:|I )should be on (?:|the )homepage$/
     */
    public function assertHomepage()
    {
        $this->spin(function ($context) {
            /* @var MinkContext $context */
            $context->assertSession()->addressEquals($this->locatePath('/'));

            return true;
        });
    }

    /**
     * @param $pattern
     * {@inheritdoc}
     * @Then /^the (?i)url(?-i) should match (?P<pattern>"(?:[^"]|\\")*")$/
     */
    public function assertUrlRegExp($pattern)
    {
        $this->spin(function ($context) use ($pattern) {
            /* @var MinkContext $context */
            $context->assertSession()->addressMatches($this->fixStepArgument($pattern));

            return true;
        });
    }

    /**
     * @param $code
     * {@inheritdoc}
     * @Then /^the response status code should be (?P<code>\d+)$/
     */
    public function assertResponseStatus($code)
    {
        $this->spin(function ($context) use ($code) {
            /* @var MinkContext $context */
            $context->assertSession()->statusCodeEquals($code);

            return true;
        });
    }

    /**
     * @param $code
     * {@inheritdoc}
     * @Then /^the response status code should not be (?P<code>\d+)$/
     */
    public function assertResponseStatusIsNot($code)
    {
        $this->spin(function ($context) use ($code) {
            /* @var MinkContext $context */
            $context->assertSession()->statusCodeNotEquals($code);

            return true;
        }, 50);
    }

    /**
     * @param $text
     * {@inheritdoc}
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageContainsText($text)
    {
        return $this->spin(function ($context) use ($text) {
            /* @var MinkContext $context */
            $context->assertSession()->pageTextContains($this->fixStepArgument($text));

            return true;
        });
    }

    /**
     * @param $text
     * {@inheritdoc}
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertPageNotContainsText($text)
    {
        $this->spin(function ($context) use ($text) {
            /* @var MinkContext $context */
            $context->assertSession()->pageTextNotContains($this->fixStepArgument($text));

            return true;
        }, 50);
    }

    /**
     * @param $pattern
     * {@inheritdoc}
     * @Then /^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/
     */
    public function assertPageMatchesText($pattern)
    {
        $this->spin(function ($context) use ($pattern) {
            /* @var MinkContext $context */
            $context->assertSession()->pageTextMatches($this->fixStepArgument($pattern));

            return true;
        });
    }

    /**
     * @param $pattern
     * {@inheritdoc}
     * @Then /^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/
     */
    public function assertPageNotMatchesText($pattern)
    {
        $this->spin(function ($context) use ($pattern) {
            /* @var MinkContext $context */
            $context->assertSession()->pageTextNotMatches($this->fixStepArgument($pattern));

            return true;
        }, 50);
    }

    /**
     * @param $text
     * {@inheritdoc}
     * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseContains($text)
    {
        $this->spin(function ($context) use ($text) {
            /* @var MinkContext $context */
            $context->assertSession()->responseContains($this->fixStepArgument($text));

            return true;
        });
    }

    /**
     * @param $text
     * {@inheritdoc}
     * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
     */
    public function assertResponseNotContains($text)
    {
        $this->spin(function ($context) use ($text) {
            /* @var MinkContext $context */
            $context->assertSession()->responseNotContains($this->fixStepArgument($text));

            return true;
        }, 50);
    }

    /**
     * @param $element
     * @param $text
     * {@inheritdoc}
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementContainsText($element, $text)
    {
        $this->spin(function ($context) use ($element, $text) {
            /* @var MinkContext $context */
            $context->assertSession()->elementTextContains('css', $element, $this->fixStepArgument($text));

            return true;
        });
    }

    /**
     * @param $element
     * @param $text
     * {@inheritdoc}
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotContainsText($element, $text)
    {
        $this->spin(function ($context) use ($element, $text) {
            /* @var MinkContext $context */
            $context->assertSession()->elementTextNotContains('css', $element, $this->fixStepArgument($text));

            return true;
        }, 50);
    }

    /**
     * @param $element
     * @param $value
     * {@inheritdoc}
     * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementContains($element, $value)
    {
        $this->spin(function ($context) use ($element, $value) {
            /* @var MinkContext $context */
            $context->assertSession()->elementContains('css', $element, $this->fixStepArgument($value));

            return true;
        });
    }

    /**
     * @param $element
     * @param $value
     * {@inheritdoc}
     * @Then /^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertElementNotContains($element, $value)
    {
        $this->spin(function ($context) use ($element, $value) {
            /* @var MinkContext $context */
            $context->assertSession()->elementNotContains('css', $element, $this->fixStepArgument($value));

            return true;
        }, 50);
    }

    /**
     * @param $element
     * {@inheritdoc}
     * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element$/
     */
    public function assertElementOnPage($element)
    {
        $this->spin(function ($context) use ($element) {
            /* @var MinkContext $context */
            $context->assertSession()->elementExists('css', $element);

            return true;
        });
    }

    /**
     * @param $element
     * {@inheritdoc}
     * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
     */
    public function assertElementNotOnPage($element)
    {
        $this->spin(function ($context) use ($element) {
            /* @var MinkContext $context */
            $context->assertSession()->elementNotExists('css', $element);

            return true;
        }, 50);
    }

    /**
     * @param $field
     * @param $value
     * {@inheritdoc}
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldContains($field, $value)
    {
        $this->spin(function ($context) use ($field, $value) {
            /** @var MinkContext $context */
            $field = $this->fixStepArgument($field);
            $value = $this->fixStepArgument($value);
            $context->assertSession()->fieldValueEquals($field, $value);

            return true;
        });
    }

    /**
     * @param $field
     * @param $value
     * {@inheritdoc}
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldNotContains($field, $value)
    {
        $this->spin(function ($context) use ($field, $value) {
            /** @var MinkContext $context */
            $field = $this->fixStepArgument($field);
            $value = $this->fixStepArgument($value);
            $context->assertSession()->fieldValueNotEquals($field, $value);

            return true;
        }, 50);
    }

    /**
     * @param $num
     * @param $element
     * {@inheritdoc}
     * @Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
     */
    public function assertNumElements($num, $element)
    {
        $this->spin(function ($context) use ($num, $element) {
            /* @var MinkContext $context */
            $context->assertSession()->elementsCount('css', $element, intval($num));

            return true;
        });
    }

    /**
     * @param $checkbox
     * {@inheritdoc}
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/
     */
    public function assertCheckboxChecked($checkbox)
    {
        $this->spin(function ($context) use ($checkbox) {
            /* @var MinkContext $context */
            $context->assertSession()->checkboxChecked($this->fixStepArgument($checkbox));

            return true;
        });
    }

    /**
     * @param $checkbox
     * {@inheritdoc}
     *
     * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/
     * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/
     */
    public function assertCheckboxNotChecked($checkbox)
    {
        $this->spin(function ($context) use ($checkbox) {
            /* @var MinkContext $context */
            $context->assertSession()->checkboxNotChecked($this->fixStepArgument($checkbox));

            return true;
        }, 50);
    }
}
