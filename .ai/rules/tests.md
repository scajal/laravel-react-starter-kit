---
paths:
  - 'tests/**'
---

# Tests

## Query Eloquent through query()
Write `Model::query()->...`, never static `Model::where()` / `Model::create()` shortcuts. There is no repository or query-object layer; query Eloquent directly where it's needed.

## PHPDoc types are fully qualified
Write class names in PHPDoc fully qualified, even when the class is imported: `@var \App\Modules\Core\Identity\Models\User $user`, `@return \Illuminate\Database\Eloquent\Relations\HasMany<\App\...\Preference, $this>`.

## Strict types
Start every PHP file with `declare(strict_types=1);`.

## Tests mirror the source tree
Place each test at the path mirroring its class: `app/Modules/X/Y.php` is tested in `tests/Feature/Modules/X/YTest.php`, or `tests/Unit/Modules/X/YTest.php` for pure classes (no database, HTTP, or Livewire) such as enums.

## Test auth helper and $this annotation
Authenticate with the `asUser()` helper from `tests/Pest.php`; use `$this->actingAs($user)` only to chain an HTTP request. Open every closure that uses `$this` with `/** @var \Tests\TestCase $this */`.
