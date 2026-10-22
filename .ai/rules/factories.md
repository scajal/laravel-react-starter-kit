---
paths:
  - 'database/factories/**'
---

# Factories

## Get key and table names from the models
Reference pivot tables and foreign keys through the models (`new Membership()->getTable()`, `new Team()->getForeignKey()`, `$relation->getForeignKeyName()`), not string literals.

## Factories mirror the model namespace
Place factories under `Database\Factories\Modules\<model namespace>` (e.g. `Database\Factories\Modules\Core\Identity\Models\TeamFactory`) and set `protected $model` explicitly.

## Keep lists alphabetical
Order migration columns (after `id`, before `timestamps()`), `#[Fillable]` lists, factory `definition()` keys, Data constructor properties (after `id`), enum cases, and model methods alphabetically.

## Strict types
Start every PHP file with `declare(strict_types=1);`.
