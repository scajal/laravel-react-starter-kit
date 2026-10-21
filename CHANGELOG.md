# Changelog

All notable changes to this starter kit are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Sign-in through WorkOS AuthKit, with inactive users signed out automatically.
- Teams with `Owner`, `Administrator` and `Member` roles, memberships and a current-team switcher.
- A Filament admin panel at `/administration` to manage users, teams and members.
- An audit trail of changes to users, teams, memberships and preferences, shown as a timeline in the admin panel.
- English and Spanish translations shared between Laravel and React.
- Light, dark and system themes, with the appearance and locale stored on the user's account.
- Server-side rendering and the React Compiler.
- Typed Inertia props through laravel-data, with generated TypeScript types and a test that fails when they drift.
- Type-safe routes through Wayfinder.
- Installation through the Laravel installer: `laravel new my-app --using=scajal/laravel-react-starter-kit`.

[Unreleased]: https://github.com/scajal/laravel-react-starter-kit/commits/main
