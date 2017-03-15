<?php

namespace Troopers\BehatContexts\Tests\Features\Context;

use Knp\FriendlyContexts\Context\Context;

class TestContext extends Context
{
    /**
     * @Then /^the parameter "(?P<name>[^"]*)" should be false$/
     **/
    public function theParameterIsFalse($name)
    {
        \PHPUnit_Framework_Assert::assertFalse(
            $this->getParameter('troopers.behatcontexts.'.$name)
        );
    }

    /**
     * @Then /^the parameter "(?P<name>[^"]*)" should be true/
     **/
    public function theParameterIsTrue($name)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $this->getParameter('troopers.behatcontexts.'.$name)
        );
    }

    /**
     * @Then /^the parameter "(?P<name>[^"]*)" should be null/
     **/
    public function theParameterIsNull($name)
    {
        \PHPUnit_Framework_Assert::assertNull(
            $this->getParameter('troopers.behatcontexts.'.$name)
        );
    }

    /**
     * @Then /^the parameter "(?P<name>[^"]*)" should have value "(?P<value>[^"]*)"$/
     **/
    public function theParameterHasValue($name, $value)
    {
        \PHPUnit_Framework_Assert::assertSame(
            $this->getParameter('troopers.behatcontexts.'.$name),
            $value
        );
    }
}
