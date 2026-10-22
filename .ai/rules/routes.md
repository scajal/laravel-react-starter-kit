---
paths:
  - 'routes/**'
---

# Routes

## Middleware by class, not alias
Attach middleware by class (`->middleware([Authenticate::class, ValidateSessionWithWorkOS::class])`), never by alias strings like `'auth'`. Authenticated routes always pair `Authenticate` with `ValidateSessionWithWorkOS`.
