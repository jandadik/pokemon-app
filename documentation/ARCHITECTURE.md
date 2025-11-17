# Pokemon Card Collection App - Architecture Overview

This document provides a high-level overview of the application architecture.

## Tech Stack Summary

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend Framework** | Laravel | 12.x |
| **PHP Version** | PHP | 8.2+ |
| **Frontend Framework** | Vue.js | 3.x (Composition API) |
| **SPA Bridge** | Inertia.js | Latest |
| **UI Components** | PrimeVue (unstyled) | Latest |
| **CSS Framework** | Tailwind CSS | 3.x |
| **State Management** | Pinia | Latest |
| **Build Tool** | Vite | 6.x |
| **Database** | MySQL/MariaDB | - |
| **Authentication** | Laravel Auth + Google2FA + WorkOS OAuth | - |
| **Authorization** | Spatie Laravel Permission | - |

## Application Structure

```
pokemon-app/
├── app/                          # Laravel Backend
│   ├── Http/
│   │   ├── Controllers/          # 28 controllers across 5 namespaces
│   │   ├── Middleware/           # Auth, 2FA, permissions, locale
│   │   ├── Requests/             # FormRequest validation classes
│   │   └── Resources/            # API resource transformers
│   ├── Models/                   # 24 Eloquent models
│   ├── Services/                 # 4 service classes (~39KB business logic)
│   ├── Policies/                 # Authorization policies
│   └── Mail/                     # Mailable classes
├── resources/
│   ├── js/
│   │   ├── app.js                # Vue app entry point
│   │   ├── Pages/                # 32 Inertia page components
│   │   ├── Components/           # ~58 reusable Vue components
│   │   ├── Layouts/              # Layout wrappers
│   │   ├── stores/               # 7 Pinia state stores
│   │   ├── composables/          # 4 Vue composable functions
│   │   ├── utils/                # Helper utilities
│   │   └── i18n/                 # Internationalization
│   ├── css/                      # Tailwind CSS + custom styles
│   └── lang/                     # Backend translations (en, cs)
├── routes/                       # 7 route files, 88 routes total
│   ├── web.php                   # Main router (imports domain routes)
│   ├── catalog.php               # Card catalog routes
│   ├── collections.php           # Collection management routes
│   ├── profile.php               # User profile routes
│   ├── auth.php                  # Authentication routes
│   ├── admin.php                 # Admin panel routes
│   └── public.php                # Public routes
├── database/
│   ├── migrations/               # Schema migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories
└── tests/
    ├── Feature/                  # Integration tests
    └── Unit/                     # Unit tests
```

## Architecture Patterns

### Backend Architecture

1. **Service Layer Pattern**
   - Business logic extracted from controllers into service classes
   - Controllers handle HTTP concerns only
   - Services encapsulate domain logic and data transformations

2. **Form Request Validation**
   - Input validation via dedicated FormRequest classes
   - Centralized validation rules
   - Authorization checks in form requests

3. **Policy-Based Authorization**
   - Laravel Policies for model-based access control
   - Spatie Permission for role/permission management
   - Gate facade for authorization checks

4. **Materialized View Pattern**
   - Complex queries delegated to database views
   - Pre-computed data for performance
   - 3 materialized view models for pricing and variants

### Frontend Architecture

1. **Inertia.js SPA**
   - Server-side routing with client-side rendering
   - No separate API layer needed
   - Props passed directly from controllers to Vue components

2. **Composition API**
   - Vue 3 Composition API with `<script setup>`
   - Composables for reusable logic
   - Reactive state management

3. **Pinia State Management**
   - Centralized state stores by domain
   - LocalStorage persistence for user preferences
   - Computed getters for derived state

4. **Component Composition**
   - Small, focused components
   - Props/emits for data flow
   - Slots for customization

## Data Flow

```
User Request
     ↓
Laravel Router (routes/*.php)
     ↓
Middleware (auth, 2fa, permissions)
     ↓
FormRequest (validation)
     ↓
Controller
     ↓
Service Layer (business logic)
     ↓
Eloquent Models / Materialized Views
     ↓
Database
     ↓
Inertia Response (props)
     ↓
Vue Page Component
     ↓
Pinia Stores (client state)
     ↓
Child Components
     ↓
User Interaction
     ↓
Inertia Router (router.visit/post/etc)
     ↓
Back to Laravel
```

## Key Statistics

| Metric | Count |
|--------|-------|
| **Laravel Models** | 24 |
| **Service Classes** | 4 |
| **Controllers** | 28 |
| **Routes** | 88 |
| **Vue Pages** | 32 |
| **Vue Components** | ~58 |
| **Pinia Stores** | 7 |
| **Composables** | 4 |
| **Supported Languages** | 2 (cs, en) |

## Security Features

- **Authentication**: Multi-factor with 2FA (Google2FA)
- **SSO**: WorkOS OAuth integration
- **Authorization**: Role/permission-based (Spatie)
- **CSRF Protection**: Automatic via Laravel/Inertia
- **XSS Prevention**: Vue auto-escaping
- **SQL Injection**: Eloquent ORM protection
- **Input Validation**: FormRequest classes

## Performance Optimizations

- **Caching**: Perpetual cache for images, strategic caching for sets/prices
- **Materialized Views**: Pre-computed complex queries
- **Lazy Loading**: Images loaded on demand with intersection observer
- **Debouncing**: Search and filter operations debounced
- **Pagination**: Server-side pagination for large datasets
- **State Persistence**: LocalStorage for user preferences

## Documentation Index

### Backend Documentation
- [Models & Relationships](./backend/MODELS.md)
- [Services & Business Logic](./backend/SERVICES.md)
- [Controllers & Endpoints](./backend/CONTROLLERS.md)
- [Routes & API](./backend/ROUTES.md)

### Frontend Documentation
- [Vue Pages](./frontend/PAGES.md)
- [Vue Components](./frontend/COMPONENTS.md)
- [Pinia Stores](./frontend/STORES.md)

## Development Workflow

1. **Backend Changes**
   ```bash
   php artisan make:model ModelName
   php artisan make:controller ControllerName
   php artisan make:request RequestName
   # Service classes created manually in app/Services/
   ```

2. **Frontend Changes**
   ```bash
   # Create page in resources/js/Pages/
   # Create components in resources/js/Components/
   # Add store in resources/js/stores/
   ```

3. **Testing**
   ```bash
   php artisan test              # Backend tests
   npm run test:unit             # Frontend tests
   ```

4. **Code Quality**
   ```bash
   ./vendor/bin/pint             # PHP formatting
   npm run fix:eslint            # JS/Vue linting
   ```

## Environment Requirements

- PHP 8.2+
- Node.js 18+
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2.x
- NPM 9.x+
