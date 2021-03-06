Installation
============

Execute

```
composer require --dev troopers/behat-contexts 
```

Edit behat.yml
```yaml
default:
    # ...
    suites:
        default:
            # ...
            contexts:
                - # ...
                - Troopers\BehatContexts\Context\MailContext
                - Troopers\BehatContexts\Context\ExtendedAliceContext
                - Troopers\BehatContexts\Context\ExtendedEntityContext
                - Troopers\BehatContexts\Context\ExtendedMinkContext
    extensions:
        # ...
        Troopers\BehatContexts\Extension: ~
```

Please care of removing 
```yaml
    - Knp\FriendlyContexts\Context\AliceContext
    - Knp\FriendlyContexts\Context\EntityContext
    - Knp\FriendlyContexts\Context\MinkContext
```
if you use our extended contexts


###Extra configuration

```yaml
        Troopers\BehatContexts\Extension:   
            alias_entity:
                enabled: true
            mails:
                path: "path/to/emailConfig/directory"
                key: mailsconfigkey
                translation:
                    firstCharacter: "%"
                    lastCharacter: "%"
```
