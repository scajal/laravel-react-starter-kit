---
paths:
  - 'app/Modules/**/Data/**'
---

# Data

## Frontend payloads are laravel-data objects
Send data to the frontend as spatie/laravel-data classes in the module's `Data/` folder (`Data/Models/<Model>Data` for models), never as Eloquent API Resources or raw models. Build them with a static `fromModel()` that returns `new self(...)` with named arguments, and wrap relations as `Lazy|XData` via `Lazy::whenLoaded()`. Regenerate the TypeScript types with `php artisan typescript:transform` instead of writing them by hand.

## Keep lists alphabetical
Order migration columns (after `id`, before `timestamps()`), `#[Fillable]` lists, factory `definition()` keys, Data constructor properties (after `id`), enum cases, and model methods alphabetically.
