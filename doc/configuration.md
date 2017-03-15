Installation
============

Execute

```
composer require troopers/behat-contexts 
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
    extensions:
        # ...
        Troopers\BehatContexts\Extension: ~
```

Please care of removing 
```yaml
    - Knp\FriendlyContexts\Context\AliceContext
    - Knp\FriendlyContexts\Context\EntityContext
```
if you use our friendly extended context



###Extra configuration

```yaml
        Troopers\BehatContexts\Extension:   
            alias_entity:
                enabled: true
            mails:
                default:
                    path: "path/to/emailConfig/directory"
                    key: mailsconfigkey
                    translation:
                        firstCharacter: "%"
                        lastCharacter: "%"
                config_2:
                    path: "…"
                    key: "…"
                    […]
```