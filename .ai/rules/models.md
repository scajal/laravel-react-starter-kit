---
paths:
  - 'app/Modules/**/Models/**'
---

# Models

## ULID keys everywhere
Models use `HasUlids` plus the `DefinesKeyType` trait annotated `/** @use \App\Modules\Support\Concerns\DefinesKeyType<string> */`. Migrations declare `$table->ulid('id')->primary()`, `foreignIdFor(Model::class)` for foreign keys, and `ulidMorphs` / `nullableUlidMorphs` for morphs.

## Every model is audited
Use `LogsActivity` and implement `HasTimeline` with `InteractsWithTimeline`. `getActivitylogOptions()` returns `LogOptions::defaults()->logAll()->logOnlyDirty()->dontLogEmptyChanges()` (add `logExcept()` for secrets), and `timeline()` returns `TimelineBuilder::make($this)->fromActivityLog()`.

## Model metadata uses attributes
Declare mass-assignable, hidden, and table names with the `#[Fillable([...])]`, `#[Hidden([...])]` and `#[Table('...')]` class attributes, not the `$fillable` / `$hidden` / `$table` properties.

## Get key and table names from the models
Reference pivot tables and foreign keys through the models (`new Membership()->getTable()`, `new Team()->getForeignKey()`, `$relation->getForeignKeyName()`), not string literals.

## Model docblocks are generated
Model `@property` / `@method` / `@mixin \Eloquent` docblocks come from laravel-ide-helper. After changing columns or relations, regenerate them with `php artisan ide-helper:models --write` instead of hand-editing.

## Keep lists alphabetical
Order migration columns (after `id`, before `timestamps()`), `#[Fillable]` lists, factory `definition()` keys, Data constructor properties (after `id`), enum cases, and model methods alphabetically.
