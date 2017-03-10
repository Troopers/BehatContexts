#Extended Entity Context

##Parent Entity Context

[KnpLabs/FriendlyContexts](https://github.com/KnpLabs/FriendlyContexts/edit/master/doc/context-entity.md)

##Truncate Data

You just have to use the tag **@truncate-data**, it's faster than reset-schema that drop and re-create the schema of db.
Truncate-data will only delete datas and not schema.
```gherkin
@truncate-data
Feature: My feature
...
```

## Generate array for TableNode

```gherkin
  Given the following users:
    | firstname | lastname | infos                       |
    | George    | Abitbol  | eyes : blue, hat: of course |
```

## Find one object

```gherkin
  Given the following users:
    | firstname | lastname |
    | George    | Abitbol  |
    | José      | Abitbol  |
  Then I should find 1 User like:
    | firstname |
    | José      |
  Then I should find 2 Users like:
    | lastname |
    | Abitbol  |
```

## Assert no object

```gherkin
  Then I should not find 3 User like:
    | firsname |
    | José     |
  Then I should not find any User like:
    | firsname |
    | Michel   |
```
