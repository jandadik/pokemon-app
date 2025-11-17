# Pokemon Card Collection App - Technical Documentation

This directory contains comprehensive technical documentation for the Pokemon Card Collection Management application.

## Documentation Structure

```
docs/
├── README.md                    # This file
├── ARCHITECTURE.md              # High-level architecture overview
├── backend/
│   ├── MODELS.md                # Laravel Eloquent models (24 models)
│   ├── SERVICES.md              # Service layer documentation (4 services)
│   ├── CONTROLLERS.md           # Controllers documentation (28 controllers)
│   └── ROUTES.md                # Routing documentation (88 routes)
└── frontend/
    ├── PAGES.md                 # Vue.js pages (32 pages)
    ├── COMPONENTS.md            # Reusable components (~58 components)
    └── STORES.md                # Pinia state management (7 stores)
```

## Quick Links

### Overview
- **[Architecture Overview](./ARCHITECTURE.md)** - Start here for a high-level understanding of the application

### Backend (Laravel)
- **[Models & Relationships](./backend/MODELS.md)** - Database models, relationships, and data structures
- **[Services](./backend/SERVICES.md)** - Business logic layer and service classes
- **[Controllers](./backend/CONTROLLERS.md)** - HTTP request handlers and API endpoints
- **[Routes](./backend/ROUTES.md)** - Application routing and URL structure

### Frontend (Vue.js)
- **[Pages](./frontend/PAGES.md)** - Inertia.js page components
- **[Components](./frontend/COMPONENTS.md)** - Reusable Vue components
- **[Stores](./frontend/STORES.md)** - Pinia state management

## Documentation Statistics

| Area | Count | Documentation |
|------|-------|---------------|
| Laravel Models | 24 | [MODELS.md](./backend/MODELS.md) |
| Laravel Services | 4 | [SERVICES.md](./backend/SERVICES.md) |
| Laravel Controllers | 28 | [CONTROLLERS.md](./backend/CONTROLLERS.md) |
| Total Routes | 88 | [ROUTES.md](./backend/ROUTES.md) |
| Vue Pages | 32 | [PAGES.md](./frontend/PAGES.md) |
| Vue Components | ~58 | [COMPONENTS.md](./frontend/COMPONENTS.md) |
| Pinia Stores | 7 | [STORES.md](./frontend/STORES.md) |

## Key Features Documented

### Backend
- Complete Eloquent model relationships and attributes
- Service layer patterns and business logic
- Controller methods and HTTP responses
- Route middleware and authorization
- Caching strategies
- Database transactions

### Frontend
- Page component props and features
- Component API (props, emits, slots)
- State management patterns
- Persistence strategies
- Authentication and authorization flows
- Dark mode and internationalization

## How to Use This Documentation

1. **New to the project?** Start with [ARCHITECTURE.md](./ARCHITECTURE.md)
2. **Working on backend?** Check [MODELS.md](./backend/MODELS.md) and [SERVICES.md](./backend/SERVICES.md)
3. **Working on frontend?** Check [COMPONENTS.md](./frontend/COMPONENTS.md) and [STORES.md](./frontend/STORES.md)
4. **Adding new features?** Reference [ROUTES.md](./backend/ROUTES.md) and [CONTROLLERS.md](./backend/CONTROLLERS.md)

## Contributing to Documentation

When adding new features to the application:

1. Update relevant documentation files
2. Follow existing formatting patterns
3. Include code examples where helpful
4. Keep information accurate and up-to-date

## Generation Date

This documentation was generated on: **2025-11-17**

Last analyzed codebase version: Current working state
