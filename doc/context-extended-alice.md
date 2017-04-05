# Extended Alice Context

## Parent Alice Context

[KnpLabs/FriendlyContexts](https://github.com/KnpLabs/FriendlyContexts/blob/master/doc/context-alice.md)

## Define Id for Alice

You can define an id for the fixture entity
```yaml
# user.yml
App\Entity\User:
    user-john:
        id: 1
        firstname: John
        lastname: Doe
```

## Alias entity

You have to enable aliasing in the `behat.yml` configuration:

```yml
default:
    extensions:
        Troopers\BehatContexts\Extension:
            alias_entity:
                enabled: true
```

Then you can alias entities with `@` in order to reuse them later:

```gherkin
@alice(User)
Feature: My feature
    The feature description
    
    Background:
        Given the following users:
            | @         | firstname | lastname |
            | @MainUser | John      | Doe      |
        And the following products:
            | name  | user      |
            | Shoes | @MainUser |
    ...
```
