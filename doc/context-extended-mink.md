#Extended Mink Context

##Parent Entity Context

[KnpLabs/FriendlyContexts](https://github.com/KnpLabs/FriendlyContexts/edit/master/doc/context-entity.md)

## Spin function

As the Behat says:

> Often, especially when using Mink to test web applications, you will find that Behat goes faster than your web application can keep up - it will try and click links or perform actions before the page has had chance to load, and therefore result in a failing test, that would have otherwise passed.
> To alleviate this problem, we can use spin functions, to repeatedly try and action or test a condition, until it works. This article looks at applying it to Mink, but the technique is applicable to any test using Behat.

[Read the doc](http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html#adding-a-timeout)

### Use the context directly (instead of its parent)

```diff
default:
    # ...
    suites:
        default:
            # ...
            contexts:
-                Knp\FriendlyContexts\Context\MinkContext
+                Troopers\BehatContexts\Context\ExtendedMinkContext
```

### Or use the Trait if you use another MinkContext

```php
<?php

namespace Acme\BehatContexts\Context;

use Behat\MinkExtension\Context\MinkContext;
use Troopers\BehatContexts\Context\SpinMinkContextTrait;

class MyMinkContext extends MinkContext
{
    use SpinMinkContextTrait;
}
```