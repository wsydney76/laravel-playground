# AGENTS.md — AI Coding Guide

## Stack
- **Laravel 13** · **Livewire 4** · **Flux UI** (livewire/flux) · **Livewire Blaze** (livewire/blaze)
- **Fortify** for authentication · **Spatie MediaLibrary v11** for file/image management
- **Tailwind CSS v4** (via `@tailwindcss/vite`, no `tailwind.config.js`) · **Vite 8**
- **Pest 4** for tests · **PHPStan level 7** (Larastan) · **Pint** (Laravel preset) for formatting
- **DDEV** for local development (SQLite in development)

## Architecture

### Routing — Two patterns coexist
- **Traditional controllers** handle public/resource routes: `ArticleController`, `HomeController`
- **Livewire Blaze pages** handle auth, dashboard, and settings via `Route::livewire()`:
  ```php
  Route::livewire('dashboard/articles', 'pages::dashboard.articles')->name('dashboard.articles');
  ```
  Blaze page components live in `resources/views/pages/` and are addressed with the `pages::` namespace.

### View Layouts
Three layouts in `resources/views/layouts/`: `app.blade.php`, `auth.blade.php`, `dashboard.blade.php`.
All UI components use Flux (`<flux:header>`, `<flux:sidebar>`, `<flux:main>`, `<flux:toast>`, etc.).

### Authentication
Fortify manages all auth flows. Views are registered in `FortifyServiceProvider::configureViews()` pointing to `pages::auth.*` Blaze views. 2FA is enabled and configured.

### Authorization
Policies use `$article->user()->is($user)` (not `$article->user_id === $user->id`) for owner checks.
Custom policy methods include `viewUnpublished` and `administer`.

### Article dual-author pattern
Articles have both `user_id` (owner, can be reassigned) and `creator_id` (immutable, set on creation). Routes use `slug` as the route key, not `id`.

### Enums carry UI metadata
`App\Enums\State` and `App\Enums\Locale` expose `label()`, `color()`, and `icon()` methods — pass the enum directly to views/components rather than mapping these values in controllers.

### Media
`Article` implements `HasMedia` via Spatie MediaLibrary. The `featured_image` collection stores on `local` disk; conversions (`featured` 1024×350, `thumb` 300×200) are stored on `dist` disk under the `conversions/` subfolder (path: `dist/conversions/{media_id}/`) and run synchronously (`nonQueued()`). Routing is handled by `App\Support\MediaLibrary\ArticlePathGenerator` registered in `config/media-library.php` under `custom_path_generators`.

### Locale
`SetLocale` middleware (appended to `web` group) reads `Auth::user()->locale` (a `Locale` enum) and calls `app()->setLocale()`. This runs as middleware (not a service provider) because auth guard resolution can happen mid-render.

### Password rules
`AppServiceProvider` sets `Password::defaults()` — stricter rules in production, relaxed in local. Always use `Password::default()` (via `PasswordValidationRules` concern) rather than hardcoding rules.

### User model
Uses PHP 8.3 attribute syntax: `#[Fillable([...])]` and `#[Hidden([...])]` instead of `$fillable`/`$hidden` arrays.

## Developer Workflows

```bash
# First-time setup
composer setup

# Local dev server (runs artisan dev / concurrently)
composer dev

# Frontend only
npm run dev

# Format Blade views with Prettier
npm run prettier-views

# Code style (PHP Pint)
composer lint          # fix
composer lint:check    # check only (used in CI)

# Static analysis
composer types:check   # PHPStan level 7

# Full test suite (config:clear + lint:check + PHPStan + Pest)
composer test

# Run Pest tests only
php artisan test
```

## Conventions
- Feature tests use `RefreshDatabase` globally (set in `tests/Pest.php`)
- All strings shown in UI pass through `__()` for i18n; translation files: `lang/en.json`, `lang/de.json`
- `CarbonImmutable` is the project-wide date class (`Date::use(CarbonImmutable::class)`)
- Destructive DB commands are blocked in production via `DB::prohibitDestructiveCommands()`
- Vite dev server is DDEV-aware — `DDEV_PRIMARY_URL_WITHOUT_PORT` env var must be set for HMR

## Key Files
| Path | Purpose |
|------|---------|
| `app/Providers/FortifyServiceProvider.php` | Auth view registration & rate limiting |
| `app/Providers/AppServiceProvider.php` | Global defaults (dates, passwords, DB safety) |
| `app/Http/Middleware/SetLocale.php` | Per-request locale from user preference |
| `app/Enums/State.php` | Article state machine with UI metadata |
| `app/Policies/ArticlePolicy.php` | Authorization logic for articles |
| `routes/web.php` + `routes/settings.php` | All web routes; settings split into own file |
| `resources/views/pages/` | Livewire Blaze page components |
| `resources/views/layouts/` | Three shared Blade layouts |

