---
paths:
  - 'app/**'
---

# App

## Code lives in modules under app/Modules
Put all application code in `app/Modules/<Module>/<Layer>/` (e.g. `Core/Identity/Models`, `Authentication/Http/Controllers`), never the default `app/Http` or `app/Models` skeleton. Shared building blocks go in `app/Modules/Support`. A module's routes live in `app/Modules/<Module>/Routes/web.php`, required from `routes/web.php`.

## Query Eloquent through query()
Write `Model::query()->...`, never static `Model::where()` / `Model::create()` shortcuts. There is no repository or query-object layer; query Eloquent directly where it's needed.

## Translate with trans() and sentence keys
Translate with `trans('Full English sentence')`, not `__()` or dotted keys, and add each new key to both `lang/en.json` and `lang/es.json`.

## PHPDoc types are fully qualified
Write class names in PHPDoc fully qualified, even when the class is imported: `@var \App\Modules\Core\Identity\Models\User $user`, `@return \Illuminate\Database\Eloquent\Relations\HasMany<\App\...\Preference, $this>`.

## Strict types
Start every PHP file with `declare(strict_types=1);`.
