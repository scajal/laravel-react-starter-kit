---
paths:
  - 'app/Modules/Support/**'
---

# Support

## Shared Inertia props have a typed contract
When adding a shared prop to `HandleInertiaRequests::share()`, add the matching property to `InertiaSharedData`, which exists only to generate the shared-props TypeScript type.
