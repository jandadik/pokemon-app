# Vue Pages Documentation

This document provides comprehensive documentation of all Inertia.js page components in the Pokemon Card Collection application.

## Pages Overview

| Category | Pages | Purpose |
|----------|-------|---------|
| Dashboard | 2 | App entry points and settings |
| Authentication | 5 | Login, registration, 2FA, password reset |
| Collections | 7 | Collection and item management |
| Card Catalog | 2 | Card browsing and details |
| Sets | 2 | Set browsing and details |
| User Account | 2 | Profile and registration |
| Admin | 13 | Role, permission, user, register management |
| **Total** | **32** | - |

---

## Page Component Structure

All pages follow this pattern:

```vue
<script setup>
import { useAuthStore } from '@/stores/authStore'

// Props from Laravel controller
defineProps({
  collection: Object,
  items: Array,
  can: Object
})

const auth = useAuthStore()
</script>

<template>
  <MainLayout>
    <!-- Page content -->
  </MainLayout>
</template>
```

---

## Dashboard & Index Pages

### Index/Index.vue
**Route:** `public.index` (GET /)

**Purpose:** Main application dashboard/settings page

**Features:**
- User preferences display
- Theme selection
- Language selection
- Notification settings overview
- 2FA status indicator

**Stores Used:**
- `useAuthStore()` - User authentication state
- `useUserStore()` - User preferences

---

### Index/Show.vue
**Route:** `public.hello` (GET /hello)

**Purpose:** Admin-only show page

**Middleware:** `auth`, `role:admin`

---

## Authentication Pages

### Auth/Login.vue
**Route:** `auth.login` (GET /login)

**Purpose:** User login form

**Props:**
```javascript
{
  errors: Object,    // Validation errors
  status: String     // Flash message
}
```

**Features:**
- Email/password form
- Password visibility toggle (eye icon)
- Remember me checkbox
- Forgot password link
- Registration link
- Form validation errors display
- Status message (e.g., password reset success)

**Form Submission:**
```javascript
router.post(route('auth.login.store'), {
  email: email.value,
  password: password.value,
  remember: remember.value
})
```

---

### Auth/ForgotPassword.vue
**Route:** `auth.password.request` (GET /forgot-password)

**Purpose:** Password reset request form

**Features:**
- Email input field
- Submit button with loading state
- Success message display
- Back to login link

---

### Auth/ResetPassword.vue
**Route:** `auth.password.reset` (GET /reset-password/{token})

**Purpose:** Password reset confirmation form

**Props:**
```javascript
{
  token: String,     // Reset token from URL
  email: String      // Pre-filled email
}
```

**Features:**
- Pre-filled email (read-only)
- New password input
- Password confirmation input
- Password strength indicator
- Form validation

---

### Auth/TwoFactorChallenge.vue
**Route:** `auth.two-factor.challenge` (GET /two-factor/challenge)

**Purpose:** 2FA TOTP code verification

**Features:**
- 6-digit code input (numbers only)
- Auto-submit on complete entry
- Recovery code option
- Timer for code expiration
- Error handling for invalid codes

**Validation:**
```javascript
// Only accept 6 numeric digits
const code = ref('')
const isValidCode = computed(() => /^\d{6}$/.test(code.value))
```

---

### Auth/VerifyEmail.vue
**Route:** `auth.verification.notice` (GET /verify-email)

**Purpose:** Email verification notice page

**Features:**
- Verification status message
- Resend verification email button
- Logout option
- Check email instructions

---

## Collections Pages

### Collections/Index.vue
**Route:** `collections.index` (GET /collections)

**Purpose:** List all user's collections

**Props:**
```javascript
{
  collections: {
    data: Array,
    links: Object,    // Pagination
    meta: Object
  },
  can: {
    create: Boolean
  }
}
```

**Features:**
- Grid/list view toggle
- Collection cards with stats
- Create new collection button
- Pagination controls
- Quick actions (default, visibility, delete)
- Empty state for no collections

**Components Used:**
- `CollectionHeader`
- `CollectionStats`
- `CollectionPagination`

---

### Collections/Create.vue
**Route:** `collections.create` (GET /collections/create)

**Purpose:** Create new collection form

**Features:**
- Collection name input
- Description textarea
- Public/private toggle
- Set as default checkbox
- Form validation
- Cancel and submit buttons

**Form Data:**
```javascript
{
  name: '',
  description: '',
  is_public: false,
  is_default: false
}
```

---

### Collections/Edit.vue
**Route:** `collections.update` (GET /collections/{collection}/edit)

**Purpose:** Edit existing collection

**Props:**
```javascript
{
  collection: Object
}
```

**Features:**
- Pre-filled form fields
- Same fields as create
- Update button
- Delete collection option
- Validation errors display

---

### Collections/Show.vue
**Route:** `collections.show` (GET /collections/{collection})

**Purpose:** View collection with all items

**Props:**
```javascript
{
  collection: Object,
  items: Array,
  can: {
    update: Boolean,
    delete: Boolean,
    setDefault: Boolean
  },
  stats: Object,
  filters: Object
}
```

**Features:**
- Collection header with actions
- Statistics panel (total cards, value, etc.)
- Grid/list view modes
- Advanced filtering panel
- Sorting options
- Pagination
- Bulk selection mode
- Add card button
- Item cards with details
- Mobile-responsive layout

**State Management:**
```javascript
const collectionsStore = useCollectionsStore()
// Manages: viewMode, filters, perPage, selectedItems, bulkMode
```

**Components Used:**
- `CollectionHeader`
- `CollectionStats`
- `CollectionFilters`
- `CollectionToolbar`
- `CollectionGridView` / `CollectionItemsTable`
- `CollectionPagination`
- `BulkActionsToolbar`
- `AddToCollectionModal`
- `CardDetailModal`

---

### Collections/Items/Create.vue
**Route:** `collections.items.create` (GET /collections/{collection}/items/create)

**Purpose:** Add card to collection (wizard flow)

**Props:**
```javascript
{
  collection: Object
}
```

**Features:**
- Step 1: Card search (autocomplete)
- Step 2: Variant selection
- Step 3: Collection item form
- Progress indicator
- Back/next navigation
- Form validation
- Loading states

**Wizard Steps:**
1. **Search Card**: `QuickCardSearch` component
2. **Select Variant**: `CardVariantSelector` component
3. **Fill Details**: `CollectionItemForm` component

**Form Fields:**
- Quantity
- Condition
- Language
- First edition checkbox
- Graded checkbox (with company/value)
- Purchase price
- Purchase date
- Notes
- Location

---

### Collections/Items/Edit.vue
**Route:** `collections.items.update` (GET /collections/{collection}/items/{item})

**Purpose:** Edit existing collection item

**Props:**
```javascript
{
  item: Object,
  collection: Object
}
```

**Features:**
- Pre-filled form with current values
- Card info display (read-only)
- Variant info display
- Update button
- Delete option
- Form validation

---

### Collections/Items/Demo.vue
**Route:** `collections.demo.card_variant_selection`

**Purpose:** Demo page for testing card/variant selection flow

**Features:**
- Development/testing tool
- Showcases component interaction
- Not for production use

---

## Card Catalog Pages

### Card/Index.vue
**Route:** `catalog.cards.index` (GET /cards)

**Purpose:** Browse and filter all cards

**Props:**
```javascript
{
  cards: {
    data: Array,
    links: Object,
    meta: Object
  },
  filters: Object
}
```

**Features:**
- Grid/list view toggle
- Search input (name, number)
- Type filter
- Rarity filter
- Set filter
- Sort options (name, number, rarity)
- Pagination
- Card image thumbnails
- Click to view details
- Add to collection button

**State Management:**
```javascript
const cardStore = useCardStore()
// Manages: search, type, rarity, set_id, sort_by, sort_direction, per_page, view_mode
```

**Components Used:**
- `CardGridView` / `CardListView`
- `CardItem`
- `LazyImage`
- Filter dropdowns

---

### Card/Show.vue
**Route:** `catalog.cards.show` (GET /cards/{card})

**Purpose:** Detailed card view with pricing

**Props:**
```javascript
{
  card: Object,
  referrer: String   // Back navigation URL
}
```

**Features:**
- Large card image
- Card name and set info
- Pokemon type badges
- HP display
- Abilities section
- Attacks with energy costs
- Rules/effects text
- Weakness/resistance
- Retreat cost
- Artist/illustrator
- Rarity
- Regulation mark
- Pricing display (TCGPlayer, CardMarket)
- Variant selector
- Add to collection button
- Back button (uses referrer)

**Components Used:**
- `PokeImage`
- `PokemonAbilities`
- `PokemonAttacks`
- `PokemonRules`
- `PricesContainer`
- `CardAdditionalInfo`
- `CardVariantSelector`
- `AddToCollectionModal`

---

## Set Pages

### Set/Index.vue
**Route:** `catalog.sets.index` (GET /sets)

**Purpose:** Browse all Pokemon card sets

**Features:**
- Set list with logos
- Series filtering
- Search by name
- Sort options
- Card count per set
- Release date display
- Click to view set cards
- Market price totals

---

### Set/Show.vue
**Route:** `catalog.sets.show` (GET /sets/{set})

**Purpose:** View set details and its cards

**Features:**
- Set logo and info
- Total cards in set
- Series name
- Release date
- Card grid/list view
- Filter cards within set
- Market value calculation
- Link to individual cards

---

## User Account Pages

### Account/Index.vue
**Route:** `user.profile` (GET /profile)

**Purpose:** User account summary and settings

**Features:**
- Tab navigation:
  - Profile (name, bio, phone)
  - Email (change email)
  - Password (change password)
  - Security (2FA, sessions)
  - Notifications (preferences)
  - Settings (theme, language)
  - Delete Account

**Components Used:**
- `ProfileTab`
- `EmailTab`
- `PasswordTab`
- `SecurityTab`
- `NotificationsTab`
- `SettingsTab`
- `DeleteAccountTab`
- `TwoFactorDialog`

**State Management:**
```javascript
const userStore = useUserStore()
// Manages: settings, login_notifications, theme, language, etc.
```

---

### UserAccount/Create.vue
**Route:** `auth.user-account.create` (GET /user-account/create)

**Purpose:** New user registration form

**Features:**
- Name input
- Email input
- Password input
- Password confirmation
- Terms and conditions checkbox
- Form validation
- Submit button with loading
- Link to login

---

## Admin Pages

### Admin/Index.vue
**Route:** `admin.index` (GET /admin)

**Purpose:** Admin dashboard

**Features:**
- Navigation cards to admin sections
- Quick stats (users, roles, permissions)
- Recent activity log
- System status indicators

**Sections:**
- User Management
- Role Management
- Permission Management
- Register Management

---

### Admin/Role/Index.vue
**Route:** `admin.roles.index` (GET /admin/roles)

**Purpose:** List all roles

**Features:**
- Role table
- Permission count per role
- User count per role
- Create new role button
- Edit/delete actions
- Permission assignment

---

### Admin/Role/Create.vue
**Route:** `admin.roles.create` (GET /admin/roles/create)

**Purpose:** Create new role

**Features:**
- Role name input
- Guard name selector
- Permission checkboxes (multi-select)
- Form validation
- Submit button

---

### Admin/Role/Edit.vue
**Route:** `admin.roles.edit` (GET /admin/roles/{role}/edit)

**Props:**
```javascript
{
  role: Object
}
```

**Features:**
- Pre-filled role form
- Edit name
- Modify permissions
- Update button
- Delete option

---

### Admin/Permission/Index.vue
**Route:** `admin.permissions.index` (GET /admin/permissions)

**Purpose:** List all permissions

**Features:**
- Permission table
- Guard name column
- Roles using permission
- Create/edit/delete actions

---

### Admin/Permission/Create.vue
**Route:** `admin.permissions.create` (GET /admin/permissions/create)

**Features:**
- Permission name input (dot notation: `resource.action`)
- Guard name selector
- Form validation

---

### Admin/Permission/Edit.vue
**Route:** `admin.permissions.edit` (GET /admin/permissions/{permission}/edit)

**Props:**
```javascript
{
  permission: Object
}
```

---

### Admin/User/Index.vue
**Route:** `admin.users.index` (GET /admin/users)

**Purpose:** List all users

**Features:**
- User table
- Email, name columns
- Role badges
- Last login date
- Create/edit/delete actions
- Pagination

---

### Admin/User/Create.vue
**Route:** `admin.users.create` (GET /admin/users/create)

**Features:**
- Name input
- Email input
- Password input
- Role selection (multi-select)
- Form validation

---

### Admin/User/Edit.vue
**Route:** `admin.users.edit` (GET /admin/users/{user}/edit)

**Props:**
```javascript
{
  user: Object
}
```

**Features:**
- Edit user details
- Change roles
- Reset password option
- Update button

---

### Admin/Register/Index.vue
**Route:** `admin.register-categories.index` (GET /admin/registers)

**Purpose:** Manage register categories

**Features:**
- Category list
- Register items per category
- Create/edit/delete categories
- Navigate to items

---

### Admin/Register/Items.vue
**Route:** `admin.registers.index` (GET /admin/registers/{category}/items)

**Purpose:** Manage register items within category

**Features:**
- Item table
- Name, type, default columns
- CRUD operations

---

## Common Page Patterns

### 1. Authentication Check
```vue
<script setup>
const auth = useAuthStore()

if (!auth.isLoggedIn) {
  router.visit(route('auth.login'))
}
</script>
```

### 2. Permission Check
```vue
<template>
  <div v-if="auth.can('admin.access')">
    <!-- Admin content -->
  </div>
</template>
```

### 3. Form Submission
```javascript
const submit = () => {
  router.post(route('collections.store'), form.value, {
    onSuccess: () => {
      notification.success('Collection created!')
    },
    onError: (errors) => {
      // Handle validation errors
    }
  })
}
```

### 4. Props Validation
```javascript
defineProps({
  collection: {
    type: Object,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  }
})
```

### 5. Computed Properties
```javascript
const totalValue = computed(() => {
  return items.value.reduce((sum, item) => sum + item.market_price, 0)
})
```

---

## Page to Route Mapping

| Vue Page | Laravel Route | Controller |
|----------|---------------|------------|
| `Pages/Index/Index.vue` | `public.index` | IndexController@index |
| `Pages/Auth/Login.vue` | `auth.login` | AuthController@create |
| `Pages/Collections/Show.vue` | `collections.show` | CollectionController@show |
| `Pages/Card/Index.vue` | `catalog.cards.index` | CardController@index |
| `Pages/Admin/User/Index.vue` | `admin.users.index` | UserController@index |

All pages automatically resolved from `resources/js/Pages/` directory by Inertia.js.
