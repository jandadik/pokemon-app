# Laravel Routes Documentation

This document provides comprehensive documentation of all routes in the Pokemon Card Collection application.

## Route Files Overview

| File | Prefix | Name Prefix | Middleware | Routes |
|------|--------|-------------|------------|--------|
| `web.php` | - | - | - | Main router (imports others) |
| `public.php` | `/` | `public.` | None | 3 |
| `auth.php` | `/` | `auth.` | Mixed | 24 |
| `catalog.php` | `/` | `catalog.` | None | 7 |
| `collections.php` | `collections/` | `collections.` | Auth | 21 |
| `profile.php` | `/` | `user.` | Auth + 2FA | 9 |
| `admin.php` | `admin/` | `admin.` | Auth + Admin | 24 |
| **Total** | - | - | - | **88** |

---

## Public Routes

**File:** `routes/public.php`

Routes accessible without authentication.

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| GET | `/` | IndexController@index | `public.index` | - |
| GET | `/hello` | IndexController@show | `public.hello` | - |
| GET | `/language/{locale}` | LanguageController@switch | `public.language.switch` | `locale` |

**Notes:**
- `/hello` route has additional middleware: `auth`, `role:admin`
- Language switch stores locale in session

---

## Authentication Routes

**File:** `routes/auth.php`

Full authentication lifecycle: login, registration, password reset, email verification, 2FA, and SSO.

### Login & Logout

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/login` | AuthController@create | `auth.login` | Guest |
| POST | `/login` | AuthController@store | `auth.login.store` | Guest |
| DELETE | `/logout` | AuthController@destroy | `auth.logout` | Auth |

### User Registration

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/user-account/create` | UserAccountController@create | `auth.user-account.create` | Guest |
| POST | `/user-account` | UserAccountController@store | `auth.user-account.store` | Guest |

### Password Reset

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/forgot-password` | PasswordResetLinkController@create | `auth.password.request` | Guest |
| POST | `/forgot-password` | PasswordResetLinkController@store | `auth.password.email` | Guest |
| GET | `/reset-password/{token}` | NewPasswordController@create | `auth.password.reset` | Guest |
| POST | `/reset-password` | NewPasswordController@store | `auth.password.store` | Guest |

**Parameters:**
- `{token}` - Password reset token from email

### Email Verification

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/verify-email` | EmailVerificationPromptController@__invoke | `auth.verification.notice` | Auth |
| GET | `/verify-email/{id}/{hash}` | VerifyEmailController@__invoke | `auth.verification.verify` | Auth, Signed, Throttle:6,1 |
| POST | `/email/verification-notification` | EmailVerificationNotificationController@store | `auth.verification.send` | Auth, Throttle:6,1 |

**Parameters:**
- `{id}` - User ID
- `{hash}` - Verification hash

### Two-Factor Authentication

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/two-factor/qr-code` | TwoFactorAuthenticationController@generateQrCode | `auth.two-factor.qr-code` | Auth |
| POST | `/two-factor/enable` | TwoFactorAuthenticationController@enable | `auth.two-factor.enable` | Auth |
| DELETE | `/two-factor/disable` | TwoFactorAuthenticationController@disable | `auth.two-factor.disable` | Auth |
| POST | `/two-factor/verify` | TwoFactorAuthenticationController@verify | `auth.two-factor.verify` | Auth |
| GET | `/two-factor/challenge` | TwoFactorAuthenticationController@challenge | `auth.two-factor.challenge` | Auth |

### WorkOS SSO (OAuth)

| Method | URI | Controller@Action | Route Name |
|--------|-----|-------------------|------------|
| GET | `/auth/workos` | WorkOSController@redirect | `auth.workos` |
| GET | `/authenticate` | WorkOSController@callback | `auth.workos.callback` |

---

## Catalog Routes

**File:** `routes/catalog.php`

Card catalog and set browsing.

### Sets

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| GET | `/sets/` | SetController@index | `catalog.sets.index` | - |
| GET | `/sets/{set}` | SetController@show | `catalog.sets.show` | `set` |
| GET | `/sets/{set}/cards` | SetController@cards | `catalog.sets.cards` | `set` |

### Cards

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| GET | `/cards/` | CardController@index | `catalog.cards.index` | - |
| GET | `/cards/{card}` | CardController@show | `catalog.cards.show` | `card` |
| GET | `/cards/{card}/variants` | CardController@variants | `catalog.cards.variants` | `card` |
| GET | `/cards/{card}/variants/{variantTypeCode}` | CardController@variantDetails | `catalog.cards.variants.details` | `card`, `variantTypeCode` |

### Card Lookup (Authenticated)

| Method | URI | Controller@Action | Route Name | Middleware |
|--------|-----|-------------------|------------|------------|
| GET | `/lookup` | CardController@performLookup | `catalog.cards.lookup` | Auth |

**Query Parameters:**
- `q` - Search query string (min 2 chars)

**Notes:**
- Model binding for `{set}` and `{card}` parameters
- Lookup requires authentication for rate limiting

---

## Collection Routes

**File:** `routes/collections.php`

User collection and item management.

**Group Settings:**
- Prefix: `collections/`
- Name Prefix: `collections.`
- Middleware: Auth (handled in controller)

### Collection List Views

| Method | URI | Controller@Action | Route Name |
|--------|-----|-------------------|------------|
| GET | `/collections/simple-list` | CollectionController@listSimple | `collections.simple-list` |
| GET | `/collections/user-list` | CollectionController@list | `collections.user-list` |

### Collection Resource (RESTful)

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| GET | `/collections/` | CollectionController@index | `collections.index` | - |
| POST | `/collections/` | CollectionController@store | `collections.store` | - |
| GET | `/collections/create` | CollectionController@create | `collections.create` | - |
| GET | `/collections/{collection}` | CollectionController@show | `collections.show` | `collection` |
| PUT | `/collections/{collection}` | CollectionController@update | `collections.update` | `collection` |
| DELETE | `/collections/{collection}` | CollectionController@destroy | `collections.destroy` | `collection` |

### Collection Quick Actions

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| PATCH | `/collections/{collection}/toggle-default` | CollectionController@toggleDefault | `collections.toggle_default` | `collection` |
| PATCH | `/collections/{collection}/toggle-visibility` | CollectionController@toggleVisibility | `collections.toggle_visibility` | `collection` |

### Collection Items - Bulk Operations

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| DELETE | `/collections/{collection}/items/bulk-delete` | CollectionItemController@bulkDelete | `collections.items.bulk_delete` | `collection` |
| POST | `/collections/{collection}/items/bulk-duplicate` | CollectionItemController@bulkDuplicate | `collections.items.bulk_duplicate` | `collection` |
| PATCH | `/collections/{collection}/items/bulk-edit` | CollectionItemController@bulkEdit | `collections.items.bulk_edit` | `collection` |
| GET | `/collections/{collection}/items/export` | CollectionItemController@export | `collections.items.export` | `collection` |

### Collection Items - RESTful Resource

| Method | URI | Controller@Action | Route Name | Params |
|--------|-----|-------------------|------------|--------|
| GET | `/collections/{collection}/items/` | CollectionItemController@index | `collections.items.index` | `collection` |
| POST | `/collections/{collection}/items/` | CollectionItemController@store | `collections.items.store` | `collection` |
| GET | `/collections/{collection}/items/create` | CollectionItemController@create | `collections.items.create` | `collection` |
| GET | `/collections/{collection}/items/{item}` | CollectionItemController@show | `collections.items.show` | `collection`, `item` |
| PUT | `/collections/{collection}/items/{item}` | CollectionItemController@update | `collections.items.update` | `collection`, `item` |
| DELETE | `/collections/{collection}/items/{item}` | CollectionItemController@destroy | `collections.items.destroy` | `collection`, `item` |

### Demo Route

| Method | URI | Route Name |
|--------|-----|------------|
| GET | `/collections/demo/card-variant-selection` | `collections.demo.card_variant_selection` |

**Notes:**
- Bulk routes defined BEFORE resource routes to prevent path conflicts
- Item parameter uses `except: ['edit']` (edit not shown, direct update)

---

## User Profile Routes

**File:** `routes/profile.php`

User profile, settings, and account management.

**Group Settings:**
- Name Prefix: `user.`
- Middleware: `auth`, `2fa`

### Profile Management

| Method | URI | Controller@Action | Route Name |
|--------|-----|-------------------|------------|
| GET | `/profile/` | ProfileController@index | `user.profile` |
| PUT | `/profile/` | ProfileController@updateProfile | `user.profile.update` |
| PUT | `/profile/password` | ProfileController@updatePassword | `user.password.update` |
| PUT | `/profile/email` | ProfileController@updateEmail | `user.email.update` |
| PUT | `/profile/notifications` | ProfileController@updateNotifications | `user.notifications.update` |
| PUT | `/profile/settings` | ProfileController@updateSettings | `user.settings.update` |
| PUT | `/profile/security` | ProfileController@updateSecurity | `user.security.update` |

### Additional User Routes

| Method | URI | Controller@Action | Route Name |
|--------|-----|-------------------|------------|
| GET | `/parameters` | ProfileController@fetchParameters | `user.parameters.fetch` |
| GET | `/login-history` | LoginHistoryController@index | `user.login-history.index` |
| DELETE | `/sessions/{session}` | SessionController@destroy | `user.sessions.destroy` |
| POST | `/email/verification-notification` | EmailVerificationNotificationController@store | `user.verification.send` |

**Notes:**
- All routes require 2FA verification if enabled
- Parameters route returns JSON for frontend store

---

## Admin Routes

**File:** `routes/admin.php`

Administration panel with RBAC management.

**Group Settings:**
- Prefix: `admin/`
- Name Prefix: `admin.`
- Middleware: `auth`, `permission:admin.access`

### Admin Dashboard

| Method | URI | Controller@Action | Route Name |
|--------|-----|-------------------|------------|
| GET | `/admin/` | AdminController@index | `admin.index` |

### Role Management

| Method | URI | Controller@Action | Route Name | Extra Middleware |
|--------|-----|-------------------|------------|------------------|
| GET | `/admin/roles/` | RoleController@index | `admin.roles.index` | `permission:role.view` |
| POST | `/admin/roles/` | RoleController@store | `admin.roles.store` | `permission:role.view` |
| GET | `/admin/roles/create` | RoleController@create | `admin.roles.create` | `permission:role.view` |
| GET | `/admin/roles/{role}` | RoleController@show | `admin.roles.show` | `permission:role.view` |
| GET | `/admin/roles/{role}/edit` | RoleController@edit | `admin.roles.edit` | `permission:role.view` |
| PUT | `/admin/roles/{role}` | RoleController@update | `admin.roles.update` | `permission:role.view` |
| DELETE | `/admin/roles/{role}` | RoleController@destroy | `admin.roles.destroy` | `permission:role.view` |

### Permission Management

| Method | URI | Controller@Action | Route Name | Extra Middleware |
|--------|-----|-------------------|------------|------------------|
| GET | `/admin/permissions/` | PermissionController@index | `admin.permissions.index` | `permission:permission.view` |
| GET | `/admin/permissions/create` | PermissionController@create | `admin.permissions.create` | `permission:permission.create` |
| POST | `/admin/permissions/` | PermissionController@store | `admin.permissions.store` | `permission:permission.create` |
| GET | `/admin/permissions/{permission}/edit` | PermissionController@edit | `admin.permissions.edit` | `permission:permission.edit` |
| PUT | `/admin/permissions/{permission}` | PermissionController@update | `admin.permissions.update` | `permission:permission.edit` |
| DELETE | `/admin/permissions/{permission}` | PermissionController@destroy | `admin.permissions.destroy` | `permission:permission.delete` |

### Register Categories Management

| Method | URI | Controller@Action | Route Name | Extra Middleware |
|--------|-----|-------------------|------------|------------------|
| GET | `/admin/registers/` | RegisterCategoryController@index | `admin.register-categories.index` | `permission:register.view` |
| POST | `/admin/registers/` | RegisterCategoryController@store | `admin.register-categories.store` | `permission:register.create` |
| PUT | `/admin/registers/{category}` | RegisterCategoryController@update | `admin.register-categories.update` | `permission:register.edit` |
| DELETE | `/admin/registers/{category}` | RegisterCategoryController@destroy | `admin.register-categories.destroy` | `permission:register.delete` |

### Register Items Management

| Method | URI | Controller@Action | Route Name | Extra Middleware |
|--------|-----|-------------------|------------|------------------|
| GET | `/admin/registers/{category}/items` | RegisterController@index | `admin.registers.index` | `permission:register.view` |
| POST | `/admin/registers/{category}/items` | RegisterController@store | `admin.registers.store` | `permission:register.create` |
| PUT | `/admin/registers/{category}/items/{register}` | RegisterController@update | `admin.registers.update` | `permission:register.edit` |
| DELETE | `/admin/registers/{category}/items/{register}` | RegisterController@destroy | `admin.registers.destroy` | `permission:register.delete` |

### User Management

| Method | URI | Controller@Action | Route Name | Extra Middleware |
|--------|-----|-------------------|------------|------------------|
| GET | `/admin/users/` | UserController@index | `admin.users.index` | `permission:user.view` |
| GET | `/admin/users/create` | UserController@create | `admin.users.create` | `permission:user.create` |
| POST | `/admin/users/` | UserController@store | `admin.users.store` | `permission:user.create` |
| GET | `/admin/users/{user}/edit` | UserController@edit | `admin.users.edit` | `permission:user.edit` |
| PUT | `/admin/users/{user}` | UserController@update | `admin.users.update` | `permission:user.edit` |
| DELETE | `/admin/users/{user}` | UserController@destroy | `admin.users.destroy` | `permission:user.delete` |

---

## API Routes

**Defined in:** `routes/web.php`

| Method | URI | Controller@Action | Purpose |
|--------|-----|-------------------|---------|
| POST | `/images/bulk` | CardImageController@getImageData | Bulk image data loading |

**Request Body:**
```json
{
    "cardIds": ["xy1-1", "xy1-2", "xy1-3"],
    "size": "small"
}
```

**Response:**
```json
{
    "xy1-1": {
        "url": "/storage/cards/xy1-1_small.png",
        "fallback": "https://...",
        "cached_at": "..."
    }
}
```

---

## Middleware Summary

### Available Middleware Aliases

```php
'auth'              => Authenticate::class
'2fa'               => TwoFactorMiddleware::class
'role'              => RoleMiddleware::class
'permission'        => PermissionMiddleware::class
'role_or_permission'=> RoleOrPermissionMiddleware::class
'track.login'       => TrackNewLogin::class
'guest'             => RedirectIfAuthenticated::class
'verified'          => EnsureEmailIsVerified::class
'signed'            => ValidateSignature::class
'throttle'          => ThrottleRequests::class
```

### Global Middleware (All Requests)

- `SetLocale` - Sets application locale from session
- `HandleInertiaRequests` - Inertia.js integration
- `TrackNewLogin` - Tracks login history

### Route Group Middleware

| Group | Middleware Stack |
|-------|------------------|
| Public | None |
| Auth (Guest) | `guest` |
| Auth (Authenticated) | `auth` |
| User Profile | `auth`, `2fa` |
| Admin | `auth`, `permission:admin.access` |

---

## Route Naming Conventions

Routes follow a consistent naming pattern:

```
{group}.{resource}.{action}
```

**Examples:**
- `collections.items.bulk_delete`
- `auth.two-factor.enable`
- `admin.users.create`
- `catalog.cards.variants.details`

**Benefits:**
- Easy to reference in code: `route('collections.show', $collection)`
- Clear hierarchy
- Consistent across application

---

## Model Binding

Laravel automatically resolves these route parameters to models:

| Parameter | Model |
|-----------|-------|
| `{collection}` | `UserCollection` |
| `{item}` | `UserCollectionItem` |
| `{card}` | `Card` |
| `{set}` | `Set` |
| `{user}` | `User` |
| `{role}` | `Role` (Spatie) |
| `{permission}` | `Permission` (Spatie) |
| `{session}` | `Session` |
| `{category}` | `RegisterCategory` |
| `{register}` | `Register` |

---

## Frontend Integration (Inertia.js)

Since this is an Inertia.js application, all routes render Vue components:

```php
// Controller
return Inertia::render('Collections/Show', [
    'collection' => $collection,
    'items' => $items
]);
```

This maps to: `resources/js/Pages/Collections/Show.vue`

### Page Resolution Convention

```
Route: GET /collections/{collection}
Controller: CollectionController@show
Returns: Inertia::render('Collections/Show', [...])
Vue Component: resources/js/Pages/Collections/Show.vue
```

---

## Security Layers

1. **Authentication**: Routes check user is logged in
2. **2FA Verification**: Sensitive routes require 2FA
3. **Permission Checks**: Granular permission middleware
4. **Rate Limiting**: Email verification throttled
5. **Signed URLs**: Email verification uses signed URLs
6. **CSRF Protection**: Automatic via Laravel

---

## Testing Routes

```bash
# List all routes
php artisan route:list

# Filter by name
php artisan route:list --name=collections

# Filter by path
php artisan route:list --path=admin

# Show route details
php artisan route:list --verbose
```
