@config
Feature: Test config

    Scenario: Full configuration
        Then the parameter "alias_entity.enabled" should be true
        Then the parameter "mails.path" should have value "src/Troopers/BehatContexts/Tests/Features/Fixtures"
        Then the parameter "mails.key" should have value "acme_emails"
        Then the parameter "mails.translation.firstCharacter" should have value "f"
        Then the parameter "mails.translation.lastCharacter" should have value "l"
