---
paths:
  - 'app/Modules/Administration/**'
---

# Administration

## Extend the app's Filament base classes
Filament resources extend `App\Modules\Administration\Resources\Resource` and panel providers extend `App\Modules\Administration\Providers\PanelProvider`, never Filament's classes directly. Each panel lives in `Administration/Panels/<Panel>/`.
