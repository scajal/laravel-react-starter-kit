---
paths:
  - 'database/**'
---

# Database

## PHPDoc types are fully qualified
Write class names in PHPDoc fully qualified, even when the class is imported: `@var \App\Modules\Core\Identity\Models\User $user`, `@return \Illuminate\Database\Eloquent\Relations\HasMany<\App\...\Preference, $this>`.
