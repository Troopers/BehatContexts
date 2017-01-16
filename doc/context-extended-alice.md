#Extended Alice Context

##Parent Alice Context

[KnpLabs/FriendlyContexts](https://github.com/KnpLabs/FriendlyContexts/edit/master/doc/context-alice.md)

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

```gherkin
@alice(User)
Feature: My feature
    The feature description
    
    Background:
        Given the following users
            | @         | firstname | lastname |
            | @MainUser | John      | Doe      |
        And the fowing products:
            | name  | user      |
            | Shoes | @MainUser |
    ...
```