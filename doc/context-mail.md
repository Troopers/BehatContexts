#Mail Context

## Examples

behat.yml

```yaml
    Troopers\BehatContexts\Extension:
        mails:
            path: "features/mailconfig"
            key: acme_emails
```

features/mailconfig/testconfig.yml

```yaml
acme_emails:
    SIMPLE_EMAIL:
        to: %userEmail%
        from: admin@admin.email
        subject: simple email about %subject%
        contents:
            strings:
                - Simple Email Content
    SIMPLE_EMAIL_WITH_CCI:
        to: %userEmail%
        from: admin@admin.email
        CCI: cci@cci.email
        subject: simple email about %subject%
        contents:
            strings:
                - Simple Email Content
```

Tests:

```gherkin 
    Then I should have the email SIMPLE_EMAIL with:
      | userEmail | JohnDoe@email.com |
      | subject   | tests             |
                  
```

For the moment there is 2 contents validator:
- strings: Test if the body has a list of string
- tables: Test if the body has a list of table

To add your custom content validator, you has to implement ContentValidatorInterface and tag 
your service like:

```yaml
services:
    my.custom.validator:
        class: path/to/Class  
        tags:
            - { name: troopers.behatcontexts.content_validator, contentType: 'mycustomtag' }
```
