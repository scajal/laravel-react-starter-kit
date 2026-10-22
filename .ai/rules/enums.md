---
paths:
  - 'app/Modules/**/Enums/**'
---

# Enums

## Keep lists alphabetical
Order migration columns (after `id`, before `timestamps()`), `#[Fillable]` lists, factory `definition()` keys, Data constructor properties (after `id`), enum cases, and model methods alphabetically.

## Enum shape
Enums are string-backed and namespaced by the model they describe (`Enums/<Model>/<Name>`). They implement Filament's `HasLabel` with a `getLabel()` `match` returning `trans()` strings, and `use AsOptions`.
