<?php

namespace Troopers\BehatContexts\Context;

use Knp\FriendlyContexts\Context\MinkContext;

/**
 * Class ExtendedMinkContext
 *
 * @package Troopers\BehatContexts\Context
 */
class ExtendedMinkContext extends MinkContext
{
    use SpinMinkContextTrait;
}
