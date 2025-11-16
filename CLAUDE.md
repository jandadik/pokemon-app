# CLAUDE.md - Pokemon Card Collection Management App

This document provides essential information for AI assistants working with this codebase.

## Project Overview

A full-stack Pokemon Trading Card Game collection management application where users can create and manage personal card collections, track card variants, conditions, grades, and prices, and browse a catalog of sets and cards.

**Tech Stack:**
- **Backend**: Laravel 12 (PHP 8.2+) with Inertia.js
- **Frontend**: Vue 3 (Composition API) with Vuetify 3
- **State Management**: Pinia with persistence
- **Build Tool**: Vite 6
- **Database**: MySQL/MariaDB
- **Testing**: PHPUnit (backend), Vitest (frontend)
- **Authentication**: Laravel Auth + 2FA (Google2FA) + OAuth (WorkOS)
- **Authorization**: Spatie Laravel Permission

## Quick Commands

```bash
# Development
npm run dev              # Start Vite dev server with HMR
php artisan serve        # Start Laravel dev server

# Build
npm run build            # Production build

# Testing
npm run test:unit        # Run frontend tests (Vitest)
php artisan test         # Run backend tests (PHPUnit)
./vendor/bin/phpunit     # Alternative PHPUnit runner

# Code Quality
npm run fix:eslint       # Auto-fix ESLint issues
./vendor/bin/pint        # PHP code formatting (Laravel Pint)

# Database
php artisan migrate      # Run migrations
php artisan db:seed      # Seed database
php artisan migrate:fresh --seed  # Reset and seed

# Development Tools
php artisan tinker       # Laravel REPL
php artisan ide-helper:generate  # Generate IDE helpers
```

## Project Structure

```
pokemon-app/
├── app/                    # Laravel backend
│   ├── Http/
│   │   ├── Controllers/    # Request handlers (organized by domain)
│   │   ├── Middleware/     # Auth, 2FA, permissions
│   │   ├── Requests/       # Form validation (FormRequest classes)
│   │   └── Resources/      # API resource transformers
│   ├── Models/             # Eloquent models (23+ models)
│   ├── Services/           # Business logic layer
│   ├── Policies/           # Authorization policies
│   └── Mail/               # Mailable classes
├── resources/
│   ├── js/
│   │   ├── app.js          # Vue app entry (Inertia, Vuetify, Pinia, i18n setup)
│   │   ├── Pages/          # Inertia page components (route-based)
│   │   ├── Components/     # Reusable Vue components
│   │   ├── Layouts/        # Layout wrappers
│   │   ├── stores/         # Pinia state management
│   │   ├── composables/    # Vue 3 composable functions
│   │   ├── utils/          # Helper functions
│   │   └── i18n/           # Internationalization setup
│   ├── css/                # SCSS stylesheets
│   └── lang/               # Backend translations (en, cs)
├── routes/
│   ├── web.php             # Main router (imports domain routes)
│   ├── catalog.php         # Card catalog routes
│   ├── collections.php     # Collection management routes
│   ├── profile.php         # User profile routes
│   └── admin.php           # Admin panel routes
├── database/
│   ├── migrations/         # Schema migrations
│   ├── seeders/            # Database seeders
│   └── factories/          # Model factories for testing
└── tests/
    ├── Feature/            # Integration tests (HTTP, controllers)
    └── Unit/               # Isolated unit tests (services, requests)
```

## Architecture Patterns

### Backend Patterns

**Service Layer Pattern**: Controllers delegate business logic to service classes.
```php
// Controller
public function store(CollectionItemStoreRequest $request)
{
    $item = $this->collectionItemService->createItem($request->validated());
    return redirect()->back();
}
```

**Form Request Validation**: Input validation via dedicated FormRequest classes in `app/Http/Requests/`.

**Authorization**: Uses Spatie Permission for role/permission checks and Laravel Policies for model-based access control.

### Frontend Patterns

**Pinia Stores**: State management with automatic localStorage persistence.
```javascript
export const useUserStore = defineStore('user', {
  state: () => ({ theme: 'light', language: 'en' }),
  persist: true,
  // ...
})
```

**Composable Functions**: Reusable logic extracted into composables (`resources/js/composables/`).
```javascript
// Example usage
const { validateField, errors } = useCollectionItemValidation()
```

**Inertia.js Pages**: Vue components in `Pages/` directory that receive props from Laravel controllers.

**Theme Management**: Light/dark mode with system preference detection and user persistence.

## Key Models & Relationships

```
User
  ├── hasMany(UserCollection)
  └── hasMany(UserParameter) - settings/preferences

UserCollection
  ├── belongsTo(User)
  └── hasMany(UserCollectionItem)

UserCollectionItem
  ├── belongsTo(UserCollection)
  ├── belongsTo(Card)
  └── belongsTo(CardsVariant) - optional
  └── Attributes: condition, language, quantity, grade, price

Card
  ├── belongsTo(Set)
  └── hasMany(CardsVariant)

CardsVariant
  ├── belongsTo(Card)
  └── Attributes: edition, print type, rarity
```

## Code Conventions

### PHP/Laravel
- PHP 8.2+ features (attributes, typed properties)
- PSR-12 coding standard (enforced by Laravel Pint)
- Service layer for business logic
- FormRequest classes for validation
- Model factories for testing
- Use `#[Test]` attributes in PHPUnit tests

### JavaScript/Vue
- **Indentation**: 2 spaces
- **Quotes**: Single quotes
- **Semicolons**: Never use them
- **Trailing commas**: Always in multiline
- **Component style**: Vue 3 Composition API with `<script setup>`
- **Translations**: Use `$t('key')` (not direct `useI18n()` imports)
- **Path aliases**: `@/` maps to `resources/js/`

### Testing Conventions

**Backend (PHPUnit)**:
```php
#[Test]
public function it_creates_collection_item(): void
{
    $this->actingAs($user);
    // Arrange, Act, Assert
}
```

**Frontend (Vitest)**:
- Tests in `__tests__/` directories alongside source
- Use `@vue/test-utils` for component mounting
- Mock axios calls and CSS imports

## Common Development Tasks

### Adding a New Feature

1. **Create migration** (if needed):
   ```bash
   php artisan make:migration create_feature_table
   ```

2. **Create model**:
   ```bash
   php artisan make:model Feature
   ```

3. **Create service** (for business logic):
   ```bash
   # Manually create in app/Services/FeatureService.php
   ```

4. **Create controller**:
   ```bash
   php artisan make:controller FeatureController
   ```

5. **Create form requests** (for validation):
   ```bash
   php artisan make:request FeatureStoreRequest
   ```

6. **Add routes** in appropriate route file (`routes/feature.php`)

7. **Create Vue page** in `resources/js/Pages/Feature/`

8. **Write tests** in both `tests/Feature/` and `tests/Unit/`

### Working with Collections

Collections are the core feature. Key files:
- `app/Services/CollectionService.php` - Collection CRUD
- `app/Services/CollectionItemService.php` - Item management
- `resources/js/Pages/Collections/` - Frontend pages
- `resources/js/stores/collections.js` - State management
- `routes/collections.php` - RESTful routes

### Working with Cards/Catalog

- `app/Services/CardService.php` - Card queries
- `resources/js/Pages/Card/` - Card browsing
- `resources/js/stores/cardStore.js` - Card state
- `routes/catalog.php` - Catalog routes

### Internationalization

**Backend**: Translations in `resources/lang/{locale}/`

**Frontend**: Use `$t()` function in templates:
```vue
<template>
  <div>{{ $t('common.save') }}</div>
</template>
```

Add new translations to both `en` and `cs` language files.

## Important Files

| File | Purpose |
|------|---------|
| `resources/js/app.js` | Vue app initialization, theme setup, global plugins |
| `vite.config.js` | Build configuration, Laravel integration |
| `vitest.config.ts` | Frontend test configuration |
| `phpunit.xml` | Backend test suite setup |
| `.eslintrc.js` | JavaScript/Vue linting rules |
| `app/Http/Kernel.php` | Middleware registration |
| `config/auth.php` | Authentication configuration |
| `config/permission.php` | Spatie Permission setup |

## Security Considerations

- **CSRF Protection**: Automatically handled by Laravel/Inertia
- **Authentication**: Multi-factor with 2FA support
- **Authorization**: Role/permission-based (Spatie) + policies
- **Input Validation**: Always use FormRequest classes
- **XSS Prevention**: Vue auto-escapes output
- **SQL Injection**: Use Eloquent ORM, avoid raw queries

## Performance Notes

- **Lazy Loading**: Images and components loaded on demand
- **State Persistence**: Pinia stores cached in localStorage
- **Debouncing**: Available via `utils/index.js` (throttle, debounce)
- **Vite HMR**: Fast hot module replacement in development

## Environment Setup

1. Copy `.env.example` to `.env`
2. Configure database connection
3. Run `php artisan key:generate`
4. Run `php artisan migrate --seed`
5. Start dev servers: `npm run dev` and `php artisan serve`

**Docker Services** (via docker-compose.yml):
- Mailhog for email testing (SMTP: 1025, Web UI: 8025)

## Git Workflow

- Feature branches from main
- Meaningful commit messages
- Run tests before committing: `npm run test:unit && php artisan test`
- Format code: `npm run fix:eslint && ./vendor/bin/pint`
