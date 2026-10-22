---
paths:
  - 'database/migrations/**'
---

# Migrations

## ULID keys everywhere
Models use `HasUlids` plus the `DefinesKeyType` trait annotated `/** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */`. Migrations declare `$table->ulid('id')->primary()`, `foreignIdFor(Model::class)` for foreign keys, and `ulidMorphs` / `nullableUlidMorphs` for morphs.

## Get key and table names from the models
Reference pivot tables and foreign keys through the models (`new Membership()->getTable()`, `new Team()->getForeignKey()`, `$relation->getForeignKeyName()`), not string literals.

## Keep lists alphabetical
Order migration columns (after `id`, before `timestamps()`), `#[Fillable]` lists, factory `definition()` keys, Data constructor properties (after `id`), enum cases, and model methods alphabetically.
