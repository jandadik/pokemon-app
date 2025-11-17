# Laravel Controllers Documentation

This document provides comprehensive documentation of all controllers in the Pokemon Card Collection application.

## Controller Statistics

| Namespace | Controllers | Methods | Primary Responsibility |
|-----------|-------------|---------|------------------------|
| Main (`App\Http\Controllers`) | 7 | ~35 | Core app features |
| Admin (`...\Admin`) | 6 | ~30 | Administration panel |
| Auth (`...\Auth`) | 10 | ~40 | Authentication flows |
| API (`...\Api`) | 1 | ~3 | Image serving |
| User (`...\User`) | 4 | ~15 | User account management |
| **Total** | **28** | **~123** | - |

---

## Main Application Controllers

### 1. CardController
**File:** `app/Http/Controllers/CardController.php`

Card catalog browsing and search functionality.

**Dependencies:**
- `CardService`
- `CardImageService`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /cards | Browse card catalog with filters and pagination |
| `show(Card $card)` | GET /cards/{card} | View card details with pricing and variants |
| `variants(Card $card)` | GET /cards/{card}/variants | List available variant types for a card |
| `variantDetails(Card $card, $variantTypeCode)` | GET /cards/{card}/variants/{code} | Get specific variant pricing details |
| `performLookup(Request $request)` | GET /lookup | Autocomplete card search (JSON response) |

**Features:**
- Full-text search on card name/number
- Caching: Sets cached for 3600s, Prices for 86400s
- Referrer tracking for back navigation
- Eager loads set and variant relationships

---

### 2. CollectionController
**File:** `app/Http/Controllers/CollectionController.php`

User collection CRUD operations.

**Dependencies:**
- `CollectionService`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /collections | List user's collections |
| `create()` | GET /collections/create | Show create collection form |
| `store(CollectionStoreRequest $request)` | POST /collections | Create new collection |
| `show(UserCollection $collection)` | GET /collections/{id} | View collection details with items |
| `update(CollectionUpdateRequest $request, UserCollection $collection)` | PUT /collections/{id} | Update collection |
| `destroy(UserCollection $collection)` | DELETE /collections/{id} | Delete collection |
| `toggleDefault(UserCollection $collection)` | PATCH /collections/{id}/toggle-default | Set as default collection |
| `toggleVisibility(UserCollection $collection)` | PATCH /collections/{id}/toggle-visibility | Toggle public/private |
| `listSimple()` | GET /collections/simple-list | Simple list for dropdowns (JSON) |
| `list()` | GET /collections/user-list | Full list with permissions |

**Features:**
- Policy-based authorization
- Statistics calculation (total value, card count)
- Pagination support
- Public/private visibility toggle

---

### 3. CollectionItemController
**File:** `app/Http/Controllers/CollectionItemController.php`

Manages individual cards within collections.

**Dependencies:**
- `CollectionItemService`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index(UserCollection $collection)` | GET /collections/{id}/items | List items with filters |
| `create(UserCollection $collection)` | GET /collections/{id}/items/create | Show add item form |
| `store(CollectionItemStoreRequest $request, UserCollection $collection)` | POST /collections/{id}/items | Add item to collection |
| `show(UserCollection $collection, UserCollectionItem $item)` | GET /collections/{id}/items/{itemId} | View item details |
| `update(CollectionItemUpdateRequest $request, UserCollection $collection, UserCollectionItem $item)` | PUT /collections/{id}/items/{itemId} | Update item |
| `destroy(UserCollection $collection, UserCollectionItem $item)` | DELETE /collections/{id}/items/{itemId} | Remove item |
| `bulkDelete(Request $request, UserCollection $collection)` | DELETE /collections/{id}/items/bulk-delete | Delete multiple items |
| `bulkDuplicate(Request $request, UserCollection $collection)` | POST /collections/{id}/items/bulk-duplicate | Duplicate multiple items |
| `bulkEdit(Request $request, UserCollection $collection)` | PATCH /collections/{id}/items/bulk-edit | Edit multiple items |
| `export(UserCollection $collection)` | GET /collections/{id}/items/export | Export items to CSV |

**Features:**
- Advanced filtering and sorting
- Pagination (configurable per page)
- Bulk operations (delete, duplicate, edit)
- CSV export functionality
- Variant type resolution

---

### 4. SetController
**File:** `app/Http/Controllers/SetController.php`

Pokemon card set browsing.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /sets | Browse all sets with filtering |
| `show(Set $set)` | GET /sets/{set} | View set details and cards |
| `cards(Set $set)` | GET /sets/{set}/cards | List cards in set with pricing |

**Features:**
- Series filtering
- Search and sorting
- Market price calculation
- Card count display

---

### 5. IndexController
**File:** `app/Http/Controllers/IndexController.php`

Application entry points.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET / | Main dashboard/settings page |
| `show()` | GET /hello | Admin-protected show page |

---

### 6. LanguageController
**File:** `app/Http/Controllers/LanguageController.php`

Locale/language switching.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `switch($locale)` | GET /language/{locale} | Switch application language |

**Features:**
- Stores locale in session
- Validates supported languages
- Redirects back after switch

---

### 7. Controller (Base)
**File:** `app/Http/Controllers/Controller.php`

Base controller class - provides foundation for all controllers.

---

## Admin Controllers

### 8. AdminController
**File:** `app/Http/Controllers/Admin/AdminController.php`

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index()` | GET /admin | `permission:admin.access` |

**Returns:** Admin dashboard page

---

### 9. RoleController
**File:** `app/Http/Controllers/Admin/RoleController.php`

Role management (RBAC).

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index()` | GET /admin/roles | `permission:role.view` |
| `create()` | GET /admin/roles/create | `permission:role.view` |
| `store(RoleStoreRequest $request)` | POST /admin/roles | `permission:role.view` |
| `show(Role $role)` | GET /admin/roles/{role} | `permission:role.view` |
| `edit(Role $role)` | GET /admin/roles/{role}/edit | `permission:role.view` |
| `update(RoleUpdateRequest $request, Role $role)` | PUT /admin/roles/{role} | `permission:role.view` |
| `destroy(Role $role)` | DELETE /admin/roles/{role} | `permission:role.view` |

**Features:**
- Spatie Laravel Permission integration
- Permission assignment to roles
- Role hierarchy management

---

### 10. PermissionController
**File:** `app/Http/Controllers/Admin/PermissionController.php`

Permission management.

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index()` | GET /admin/permissions | `permission:permission.view` |
| `create()` | GET /admin/permissions/create | `permission:permission.create` |
| `store(PermissionStoreRequest $request)` | POST /admin/permissions | `permission:permission.create` |
| `edit(Permission $permission)` | GET /admin/permissions/{permission}/edit | `permission:permission.edit` |
| `update(PermissionUpdateRequest $request, Permission $permission)` | PUT /admin/permissions/{permission} | `permission:permission.edit` |
| `destroy(Permission $permission)` | DELETE /admin/permissions/{permission} | `permission:permission.delete` |

---

### 11. UserController
**File:** `app/Http/Controllers/Admin/UserController.php`

User management.

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index()` | GET /admin/users | `permission:user.view` |
| `create()` | GET /admin/users/create | `permission:user.create` |
| `store(UserStoreRequest $request)` | POST /admin/users | `permission:user.create` |
| `edit(User $user)` | GET /admin/users/{user}/edit | `permission:user.edit` |
| `update(UserUpdateRequest $request, User $user)` | PUT /admin/users/{user} | `permission:user.edit` |
| `destroy(User $user)` | DELETE /admin/users/{user} | `permission:user.delete` |

**Features:**
- Role assignment
- Password management
- User CRUD with granular permissions

---

### 12. RegisterCategoryController
**File:** `app/Http/Controllers/Admin/RegisterCategoryController.php`

Register category management.

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index()` | GET /admin/registers | `permission:register.view` |
| `store(RegisterCategoryStoreRequest $request)` | POST /admin/registers | `permission:register.create` |
| `update(RegisterCategoryUpdateRequest $request, RegisterCategory $category)` | PUT /admin/registers/{category} | `permission:register.edit` |
| `destroy(RegisterCategory $category)` | DELETE /admin/registers/{category} | `permission:register.delete` |

---

### 13. RegisterController
**File:** `app/Http/Controllers/Admin/RegisterController.php`

Register items management.

**Methods:**

| Method | Route | Middleware |
|--------|-------|------------|
| `index(RegisterCategory $category)` | GET /admin/registers/{category}/items | `permission:register.view` |
| `store(RegisterStoreRequest $request, RegisterCategory $category)` | POST /admin/registers/{category}/items | `permission:register.create` |
| `update(RegisterUpdateRequest $request, RegisterCategory $category, Register $register)` | PUT /admin/registers/{category}/items/{register} | `permission:register.edit` |
| `destroy(RegisterCategory $category, Register $register)` | DELETE /admin/registers/{category}/items/{register} | `permission:register.delete` |

---

## Authentication Controllers

### 14. AuthController
**File:** `app/Http/Controllers/Auth/AuthController.php`

Login and logout handling.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `create()` | GET /login | Show login form |
| `store(LoginRequest $request)` | POST /login | Process login |
| `destroy(Request $request)` | DELETE /logout | Logout user |

**Features:**
- Session regeneration
- Remember me functionality
- 2FA challenge redirect if enabled
- Status message handling

---

### 15. TwoFactorAuthenticationController
**File:** `app/Http/Controllers/Auth/TwoFactorAuthenticationController.php`

Two-factor authentication management.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `generateQrCode()` | GET /two-factor/qr-code | Generate TOTP QR code |
| `enable(Request $request)` | POST /two-factor/enable | Enable 2FA for user |
| `disable(Request $request)` | DELETE /two-factor/disable | Disable 2FA |
| `verify(Request $request)` | POST /two-factor/verify | Verify TOTP code |
| `challenge()` | GET /two-factor/challenge | Show 2FA challenge page |

**Features:**
- Google2FA integration
- Encrypted secret storage
- Recovery codes support
- Session-based challenge flow

---

### 16. PasswordResetLinkController
**File:** `app/Http/Controllers/Auth/PasswordResetLinkController.php`

Password reset request handling.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `create()` | GET /forgot-password | Show forgot password form |
| `store(Request $request)` | POST /forgot-password | Send reset link email |

---

### 17. NewPasswordController
**File:** `app/Http/Controllers/Auth/NewPasswordController.php`

Password reset confirmation.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `create(Request $request)` | GET /reset-password/{token} | Show reset form |
| `store(Request $request)` | POST /reset-password | Process password reset |

---

### 18. ConfirmablePasswordController
**File:** `app/Http/Controllers/Auth/ConfirmablePasswordController.php`

Password confirmation for sensitive actions.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `show()` | GET /confirm-password | Show confirmation form |
| `store(Request $request)` | POST /confirm-password | Confirm password |

---

### 19. EmailVerificationPromptController
**File:** `app/Http/Controllers/Auth/EmailVerificationPromptController.php`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `__invoke()` | GET /verify-email | Show verification notice |

---

### 20. EmailVerificationNotificationController
**File:** `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `store(Request $request)` | POST /email/verification-notification | Resend verification email |

**Middleware:** `throttle:6,1`

---

### 21. VerifyEmailController
**File:** `app/Http/Controllers/Auth/VerifyEmailController.php`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `__invoke(EmailVerificationRequest $request)` | GET /verify-email/{id}/{hash} | Verify email address |

**Middleware:** `signed`, `throttle:6,1`

---

### 22. WorkOSController
**File:** `app/Http/Controllers/Auth/WorkOSController.php`

OAuth/SSO via WorkOS.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `redirect()` | GET /auth/workos | Redirect to WorkOS |
| `callback()` | GET /authenticate | Handle OAuth callback |

---

### 23. PasswordController
**File:** `app/Http/Controllers/Auth/PasswordController.php`

Password change functionality.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `update(Request $request)` | PUT /password | Update password |

---

## User Account Controllers

### 24. ProfileController
**File:** `app/Http/Controllers/User/ProfileController.php`

User profile and settings management.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /profile | Show profile page |
| `updateProfile(ProfileUpdateRequest $request)` | PUT /profile | Update basic profile |
| `updatePassword(Request $request)` | PUT /profile/password | Change password |
| `updateEmail(Request $request)` | PUT /profile/email | Update email |
| `updateNotifications(Request $request)` | PUT /profile/notifications | Update notification preferences |
| `updateSettings(Request $request)` | PUT /profile/settings | Update app settings |
| `updateSecurity(Request $request)` | PUT /profile/security | Update security settings |
| `fetchParameters()` | GET /parameters | Fetch user parameters (JSON) |

---

### 25. UserAccountController
**File:** `app/Http/Controllers/User/UserAccountController.php`

User registration.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `create()` | GET /user-account/create | Show registration form |
| `store(UserAccountStoreRequest $request)` | POST /user-account | Process registration |

**Features:**
- Password hashing
- Welcome notification
- Automatic login after registration

---

### 26. SessionController
**File:** `app/Http/Controllers/User/SessionController.php`

Session management.

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `destroy(Session $session)` | DELETE /sessions/{session} | Terminate specific session |

---

### 27. LoginHistoryController
**File:** `app/Http/Controllers/User/LoginHistoryController.php`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /login-history | List user's login history (JSON) |

---

## API Controllers

### 28. CardImageController
**File:** `app/Http/Controllers/Api/CardImageController.php`

Image data serving with caching.

**Dependencies:**
- `CardImageService`

**Methods:**

| Method | Route | Description |
|--------|-------|-------------|
| `getImageData(Request $request)` | POST /images/bulk | Bulk load image data (JSON) |

**Features:**
- Accepts array of card IDs
- Returns cached image URLs
- Aggressive caching (forever)
- Supports size parameter (small/large)

**Response Format:**
```json
{
    "cardId": {
        "url": "/storage/cards/xy1-1_small.png",
        "fallback": "https://images.pokemontcg.io/xy1/1.png",
        "cached_at": "2024-01-15T10:30:00Z"
    }
}
```

---

## Common Patterns

### 1. Service Injection
```php
public function __construct(
    private CardService $cardService
) {}
```

### 2. FormRequest Validation
```php
public function store(CollectionStoreRequest $request)
{
    $validated = $request->validated();
    // ...
}
```

### 3. Inertia Responses
```php
return Inertia::render('Collections/Show', [
    'collection' => $collection,
    'items' => $items,
    'can' => [
        'update' => Gate::allows('update', $collection),
        'delete' => Gate::allows('delete', $collection),
    ]
]);
```

### 4. Policy-Based Authorization
```php
$this->authorize('update', $collection);
// or
Gate::allows('update', $collection)
```

### 5. JSON Responses
```php
return response()->json([
    'data' => $results,
    'message' => 'Success'
]);
```

### 6. Redirects
```php
return redirect()
    ->route('collections.show', $collection)
    ->with('success', 'Collection created!');
```

---

## Caching Strategy in Controllers

| Controller | Cache Key | TTL | Purpose |
|------------|-----------|-----|---------|
| CardController | Sets | 3600s | Set list caching |
| CardController | Prices | 86400s | Price data caching |
| CardImageController | Images | Forever | Image URL caching |

---

## Security Features

1. **Authentication Middleware**: Most routes require `auth`
2. **2FA Middleware**: User routes require `2fa`
3. **Permission Middleware**: Admin routes use granular permissions
4. **CSRF Protection**: Automatic via Laravel/Inertia
5. **Rate Limiting**: Email verification throttled
6. **Signed URLs**: Email verification uses signed URLs
7. **Password Hashing**: Bcrypt via Laravel's `Hash` facade
8. **Session Regeneration**: On login/logout
