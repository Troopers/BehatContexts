@default
Feature: Test config

    Scenario: Empty configuration
        Then the parameter "alias_entity.enabled" should be false
        Then the parameter "mails.path" should be null
        Then the parameter "mails.key" should be null
        Then the parameter "mails.translation.firstCharacter" should have value "%"
        Then the parameter "mails.translation.lastCharacter" should have value "%"
