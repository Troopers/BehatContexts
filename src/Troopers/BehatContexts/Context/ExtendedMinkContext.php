<?php

namespace Troopers\BehatContexts\Context;

use Knp\FriendlyContexts\Context\MinkContext;

/**
 * Class ExtendedMinkContext.
 */
class ExtendedMinkContext extends MinkContext
{
    use SpinMinkContextTrait;
}
