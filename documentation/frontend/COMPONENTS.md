# Vue Components Documentation

This document provides comprehensive documentation of all reusable Vue components in the Pokemon Card Collection application.

## Component Statistics

| Category | Components | Purpose |
|----------|------------|---------|
| Account | 8 | User profile tabs and settings |
| Card | 11 | Card display and interaction |
| Collections | 17 | Collection management UI |
| Collection Items | 10 | Item form inputs |
| Layout | 4 | Application structure |
| UI | 4 | Common UI elements |
| Global | 3 | Theme, language, misc |
| **Total** | **~58** | - |

---

## Account Components

**Location:** `resources/js/Components/Account/`

### ProfileTab.vue
**Purpose:** User profile information editing

**Features:**
- Name input field
- Phone number input
- Bio/description textarea
- Save button with loading state
- Validation error display

**Emits:**
- `update:profile` - Profile data updated

---

### PasswordTab.vue
**Purpose:** Password change form

**Features:**
- Current password input
- New password input
- Confirm password input
- Password strength indicator
- Form validation
- Submit button

**Validation:**
- Minimum 8 characters
- Must contain uppercase, lowercase, number
- Confirmation must match

---

### EmailTab.vue
**Purpose:** Email management and verification

**Features:**
- Current email display
- New email input
- Email verification status badge
- Resend verification button
- Save button

**Status Indicators:**
- Verified (green badge)
- Pending verification (yellow badge)
- Not verified (red badge)

---

### SecurityTab.vue
**Purpose:** Security settings and 2FA management

**Features:**
- 2FA status display
- Enable/disable 2FA button
- Active sessions list
- Revoke session button
- Last login info
- Suspicious activity alerts

**Components Used:**
- `TwoFactorDialog`

---

### NotificationsTab.vue
**Purpose:** Notification preferences

**Features:**
- Email notifications toggle
- Push notifications toggle (future)
- Newsletter subscription toggle
- Login notification toggle
- Save button

**Form Data:**
```javascript
{
  email_notifications: true,
  push_notifications: false,
  newsletter: false,
  login_notifications: true
}
```

---

### SettingsTab.vue
**Purpose:** Application preferences

**Features:**
- Theme selector (light/dark/system)
- Language selector (cs/en)
- Auto-save to default collection toggle
- Other app preferences
- Save button

**Stores Used:**
- `useUserStore()` - Theme and language preferences

---

### DeleteAccountTab.vue
**Purpose:** Account deletion (destructive action)

**Features:**
- Warning message
- Password confirmation input
- Confirmation dialog
- Delete button (red, destructive)
- Irreversible action notice

**Emits:**
- `account:deleted` - Account deletion confirmed

---

### TwoFactorDialog.vue
**Purpose:** 2FA setup and management dialog

**Features:**
- QR code display for authenticator apps
- Manual entry key display
- 6-digit verification code input
- Enable/disable actions
- Recovery codes display
- Success/error feedback

**Props:**
```javascript
{
  visible: Boolean,
  enabled: Boolean
}
```

**Emits:**
- `close` - Dialog closed
- `enabled` - 2FA successfully enabled
- `disabled` - 2FA successfully disabled

---

## Card Components

**Location:** `resources/js/Components/Card/`

### CardGridView.vue
**Purpose:** Grid layout for displaying cards

**Props:**
```javascript
{
  cards: Array,
  loading: Boolean,
  selectable: Boolean
}
```

**Features:**
- Responsive grid (1-4 columns based on viewport)
- Card thumbnails
- Hover effects
- Click to view details
- Selection checkboxes (when selectable)

---

### CardListView.vue
**Purpose:** List/table layout for cards

**Props:**
```javascript
{
  cards: Array,
  loading: Boolean,
  columns: Array
}
```

**Features:**
- Table format with columns
- Sortable headers
- Compact view
- Action buttons per row

---

### CardItem.vue
**Purpose:** Individual card display item

**Props:**
```javascript
{
  card: Object,
  size: String,        // 'small', 'medium', 'large'
  showPrice: Boolean,
  clickable: Boolean
}
```

**Features:**
- Card image
- Card name
- Set badge
- Rarity indicator
- Market price (optional)
- Click handler

**Emits:**
- `click` - Card clicked

---

### CardVariantSelector.vue
**Purpose:** Select card variant type

**Props:**
```javascript
{
  cardId: String,
  selectedVariant: Object
}
```

**Features:**
- List available variants (normal, reverse holo, etc.)
- Variant type badges
- Price preview per variant
- Radio button selection
- Loading state

**Emits:**
- `select` - Variant selected

---

### QuickCardSearch.vue
**Purpose:** Autocomplete card search input

**Props:**
```javascript
{
  placeholder: String,
  autofocus: Boolean
}
```

**Features:**
- Search input with debounce (300ms)
- Autocomplete dropdown
- Card thumbnails in results
- Set name display
- Keyboard navigation (arrow keys, enter)
- Click to select
- Loading indicator

**Emits:**
- `select` - Card selected from results

**API Call:**
```javascript
// GET /lookup?q=searchTerm
const results = await axios.get(route('catalog.cards.lookup'), {
  params: { q: searchTerm }
})
```

---

### PokeImage.vue
**Purpose:** Pokemon card image display with fallback

**Props:**
```javascript
{
  src: String,
  fallback: String,
  alt: String,
  size: String,
  lazy: Boolean
}
```

**Features:**
- Primary image source
- Fallback on error
- Lazy loading support
- Placeholder while loading
- Responsive sizing
- Error state handling

---

### PokemonAbilities.vue
**Purpose:** Display Pokemon abilities

**Props:**
```javascript
{
  abilities: Array
}
```

**Features:**
- Ability name
- Ability type badge
- Ability description text
- Multiple abilities support

---

### PokemonAttacks.vue
**Purpose:** Display Pokemon attacks with costs

**Props:**
```javascript
{
  attacks: Array
}
```

**Features:**
- Attack name
- Energy cost icons (colored by type)
- Damage value
- Attack description
- Multiple attacks support

**Energy Types:**
- Fire (red)
- Water (blue)
- Grass (green)
- Lightning (yellow)
- Psychic (purple)
- Fighting (brown)
- Darkness (dark gray)
- Metal (silver)
- Fairy (pink)
- Dragon (gold)
- Colorless (white)

---

### PokemonRules.vue
**Purpose:** Display Pokemon rules text

**Props:**
```javascript
{
  rules: Array
}
```

**Features:**
- Rules list
- Special formatting for keywords
- EX/GX/V rules
- Ancient trait rules

---

### PricesContainer.vue
**Purpose:** Display card pricing from multiple sources

**Props:**
```javascript
{
  prices: Object,
  currency: String
}
```

**Features:**
- TCGPlayer prices (market, low, mid, high)
- CardMarket prices (trend, avg1, avg7, avg30)
- Currency formatting
- Price trend indicators (up/down arrows)
- Last updated timestamp

---

### CardAdditionalInfo.vue
**Purpose:** Card metadata display

**Props:**
```javascript
{
  card: Object
}
```

**Features:**
- Artist/illustrator name
- Rarity badge
- Regulation mark
- Collector number
- Legalities (Standard, Expanded, Unlimited)
- Weakness type and value
- Resistance type and value
- Retreat cost icons

---

### VariantTypeItem.vue
**Purpose:** Single variant type display

**Props:**
```javascript
{
  variant: Object,
  selected: Boolean
}
```

**Features:**
- Variant type name
- Price info
- Selection indicator
- Click to select

---

## Collections Components

**Location:** `resources/js/Components/Collections/`

### CollectionHeader.vue
**Purpose:** Collection title and action buttons

**Props:**
```javascript
{
  collection: Object,
  can: Object
}
```

**Features:**
- Collection name (editable if permission)
- Description
- Public/private badge
- Default badge
- Action buttons:
  - Edit collection
  - Set as default
  - Toggle visibility
  - Delete collection
  - Add card

---

### CollectionStats.vue
**Purpose:** Collection statistics panel

**Props:**
```javascript
{
  stats: Object,
  loading: Boolean
}
```

**Features:**
- Total cards count
- Unique cards count
- Total purchase value
- Total market value
- Value difference (profit/loss)
- Loading skeleton

**Display:**
```
Total Cards: 150
Unique: 45
Purchase Value: $1,250.50
Market Value: $1,450.75
Difference: +$200.25 (green if positive)
```

---

### CollectionFilters.vue
**Purpose:** Advanced filtering panel

**Props:**
```javascript
{
  filters: Object,
  options: Object
}
```

**Features:**
- Search input (card name/number)
- Condition dropdown
- Language dropdown
- Rarity dropdown
- Price range slider
- Date range picker
- Clear all filters button
- Apply button

**Emits:**
- `update:filters` - Filters changed

---

### CollectionToolbar.vue
**Purpose:** View mode and bulk actions toolbar

**Props:**
```javascript
{
  viewMode: String,
  bulkMode: Boolean,
  itemCount: Number
}
```

**Features:**
- Grid/list view toggle buttons
- Bulk mode toggle
- Items per page selector
- Show stats toggle
- Sort dropdown

**Emits:**
- `update:viewMode`
- `update:bulkMode`
- `update:perPage`
- `update:showStats`

---

### CollectionGridView.vue
**Purpose:** Grid layout for collection items

**Props:**
```javascript
{
  items: Array,
  loading: Boolean,
  selectable: Boolean,
  selectedItems: Set
}
```

**Features:**
- Responsive grid
- Item cards with images
- Quantity badges
- Condition indicators
- Market price display
- Selection checkboxes
- Click to view/edit

**Emits:**
- `item:click`
- `item:select`

---

### CollectionItemsTable.vue
**Purpose:** Table view for collection items

**Props:**
```javascript
{
  items: Array,
  loading: Boolean,
  sortBy: String,
  sortDirection: String
}
```

**Features:**
- Sortable columns
- Compact data display
- Action buttons
- Selection checkboxes
- Pagination integration

**Columns:**
- Image thumbnail
- Card name
- Set
- Variant
- Condition
- Language
- Quantity
- Purchase price
- Market price
- Actions

---

### CollectionPagination.vue
**Purpose:** Pagination controls

**Props:**
```javascript
{
  currentPage: Number,
  totalPages: Number,
  perPage: Number,
  total: Number
}
```

**Features:**
- Page number buttons
- Previous/next buttons
- First/last buttons
- Per page selector
- Total items display
- Jump to page input

**Emits:**
- `page:change`
- `perPage:change`

---

### CollectionEmptyStates.vue
**Purpose:** Empty state messages

**Props:**
```javascript
{
  type: String  // 'no-items', 'no-results', 'loading'
}
```

**Features:**
- Contextual empty state messages
- Illustration/icon
- Action button (e.g., "Add your first card")
- Different states for different scenarios

---

### AddToCollectionModal.vue
**Purpose:** Modal to add card to collection

**Props:**
```javascript
{
  visible: Boolean,
  card: Object
}
```

**Features:**
- Collection selector dropdown
- Quick create collection button
- Variant type selector
- Basic form fields
- Add button
- Loading state

**Emits:**
- `close`
- `added`

---

### CardDetailModal.vue
**Purpose:** View card details from collection

**Props:**
```javascript
{
  visible: Boolean,
  item: Object
}
```

**Features:**
- Card image
- Collection item details
- Purchase info
- Market value
- Edit button
- Delete button
- Close button

**Emits:**
- `close`
- `edit`
- `delete`

---

### BulkActionsToolbar.vue
**Purpose:** Toolbar for bulk operations

**Props:**
```javascript
{
  selectedCount: Number,
  items: Array
}
```

**Features:**
- Selected items count
- Select all button
- Clear selection button
- Bulk actions:
  - Delete selected
  - Duplicate selected
  - Edit selected
  - Export selected

**Emits:**
- `selectAll`
- `clearSelection`
- `bulkDelete`
- `bulkDuplicate`
- `bulkEdit`
- `bulkExport`

---

### SkeletonLoader.vue
**Purpose:** Loading skeleton for views

**Props:**
```javascript
{
  type: String,  // 'grid', 'table', 'stats'
  count: Number
}
```

**Features:**
- Animated pulse effect
- Matches actual component layout
- Configurable count

---

## Collection Item Form Components

**Location:** `resources/js/Components/Collections/CollectionItem/`

### CollectionItemForm.vue
**Purpose:** Main form for creating/editing items

**Props:**
```javascript
{
  card: Object,
  variant: Object,
  initialData: Object,
  submitLabel: String
}
```

**Features:**
- All form fields combined
- Validation handling
- Submit and cancel buttons
- Loading states

**Sub-components Used:**
- `ConditionSelect`
- `LanguageSelect`
- `QuantityInput`
- `PriceInput`
- `LocationInput`
- `GradingSection`
- `FirstEditionCheckbox`
- `NoteInput`

---

### ConditionSelect.vue
**Purpose:** Card condition dropdown

**Props:**
```javascript
{
  modelValue: String,
  error: String
}
```

**Options:**
- Mint
- Near Mint
- Excellent
- Good
- Light Played
- Played
- Poor

**Emits:**
- `update:modelValue`

---

### LanguageSelect.vue
**Purpose:** Card language selector

**Props:**
```javascript
{
  modelValue: String,
  error: String
}
```

**Options:**
- English (en)
- Japanese (jp)
- German (de)
- French (fr)
- Italian (it)
- Spanish (es)
- Portuguese (pt)
- Korean (ko)
- Chinese Traditional (zh-TW)
- Chinese Simplified (zh-CN)

---

### QuantityInput.vue
**Purpose:** Quantity number input

**Props:**
```javascript
{
  modelValue: Number,
  min: Number,
  max: Number,
  error: String
}
```

**Features:**
- Number input
- Increment/decrement buttons
- Min/max validation
- Error display

---

### PriceInput.vue
**Purpose:** Purchase price input

**Props:**
```javascript
{
  modelValue: Number,
  currency: String,
  error: String
}
```

**Features:**
- Decimal input (2 places)
- Currency symbol
- Validation
- Formatting on blur

---

### LocationInput.vue
**Purpose:** Storage location input

**Props:**
```javascript
{
  modelValue: String,
  error: String
}
```

**Features:**
- Text input
- Placeholder text
- Error handling

---

### GradingSection.vue
**Purpose:** Grading and certification fields

**Props:**
```javascript
{
  isGraded: Boolean,
  gradeCompany: String,
  gradeValue: Number
}
```

**Features:**
- Is graded checkbox
- Grade company selector (PSA, BGS, CGC, etc.)
- Grade value input (1-10 scale)
- Conditional display (only shows fields if graded)

**Emits:**
- `update:isGraded`
- `update:gradeCompany`
- `update:gradeValue`

---

### FirstEditionCheckbox.vue
**Purpose:** First edition toggle

**Props:**
```javascript
{
  modelValue: Boolean,
  error: String
}
```

**Features:**
- Checkbox input
- Label
- Info tooltip

---

### NoteInput.vue
**Purpose:** Notes/comments textarea

**Props:**
```javascript
{
  modelValue: String,
  maxLength: Number,
  error: String
}
```

**Features:**
- Multiline textarea
- Character count
- Max length validation

---

## Layout Components

**Location:** `resources/js/Components/Layout/`

### AppHeader.vue
**Purpose:** Fixed top navigation header

**Features:**
- App logo and title
- Menu toggle button (mobile)
- Right-side actions slot
- Sticky positioning
- Dark mode support

---

### AppHeaderActions.vue
**Purpose:** Header right-side actions

**Features:**
- Notification bell icon (with badge)
- Theme switcher
- Language switcher
- User menu dropdown
- Logout button

**Components Used:**
- `ThemeSwitcher`
- `LanguageSwitcher`

---

### AppNavDrawer.vue
**Purpose:** Navigation sidebar/drawer

**Props:**
```javascript
{
  open: Boolean
}
```

**Features:**
- User profile section (avatar, name, email)
- Navigation menu items
- Collapsible groups
- Active state highlighting
- Mobile overlay
- Close button

**Menu Items:**
- Dashboard
- Collections
- Card Catalog
- Sets
- Profile
- Admin (if permission)

---

### AppTabs.vue
**Purpose:** Navigation tabs below header

**Features:**
- Tab buttons
- Active tab indicator
- Responsive (scrollable on mobile)
- Route-based active state

**Stores Used:**
- `useTabStore()`

---

## UI Components

**Location:** `resources/js/Components/UI/`

### ApplicationLogo.vue
**Purpose:** App logo display

**Props:**
```javascript
{
  size: String  // 'small', 'medium', 'large'
}
```

**Features:**
- SVG logo
- Configurable size
- Alt text for accessibility

---

### NotificationToasts.vue
**Purpose:** Global toast notification container

**Features:**
- Positioned top-right
- Stacking notifications
- Auto-dismiss
- Close button
- Type-based styling (success, error, warning, info)
- Slide-in animation

**Stores Used:**
- `useNotificationStore()`

**Toast Structure:**
```javascript
{
  id: String,
  message: String,
  type: String,
  timeout: Number,
  closable: Boolean
}
```

---

### LazyImage.vue
**Purpose:** Image with lazy loading

**Props:**
```javascript
{
  src: String,
  alt: String,
  placeholder: String
}
```

**Features:**
- Intersection Observer API
- Loads when visible
- Placeholder while loading
- Smooth fade-in transition
- Error handling

---

## Global Components

**Location:** `resources/js/Components/`

### ThemeSwitcher.vue
**Purpose:** Dark/light/system theme toggle

**Features:**
- Icon buttons (sun, moon, system)
- Current theme indicator
- Click to cycle through options
- Stores preference in Pinia

**Stores Used:**
- `useUserStore()` - Theme preference

---

### LanguageSwitcher.vue
**Purpose:** Language selector dropdown

**Features:**
- Current language display (flag + code)
- Dropdown with available languages
- Click to switch
- Page reload after switch

**Languages:**
- Czech (cs)
- English (en)

**Stores Used:**
- `useUserStore()` - Language preference

---

## Component Design Patterns

### 1. Props Validation
```javascript
defineProps({
  collection: {
    type: Object,
    required: true,
    validator: (value) => value.id && value.name
  }
})
```

### 2. Event Emission
```javascript
const emit = defineEmits(['update:modelValue', 'save', 'cancel'])

const handleSave = () => {
  emit('save', formData.value)
}
```

### 3. v-model Support
```javascript
// Parent
<ConditionSelect v-model="item.condition" />

// Component
const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})
```

### 4. Computed Properties
```javascript
const isValid = computed(() => {
  return form.name.length > 0 && form.email.includes('@')
})
```

### 5. Watchers
```javascript
watch(
  () => props.filters,
  (newFilters) => {
    applyFilters(newFilters)
  },
  { deep: true }
)
```

### 6. Lifecycle Hooks
```javascript
onMounted(() => {
  loadInitialData()
})

onUnmounted(() => {
  cleanup()
})
```

---

## Styling Conventions

Components use Tailwind CSS utilities:

```vue
<template>
  <div class="bg-surface dark:bg-surface-dark p-4 rounded-lg shadow">
    <h2 class="text-lg font-semibold text-primary">
      {{ title }}
    </h2>
  </div>
</template>
```

**Common Classes:**
- `bg-surface` / `bg-surface-dark` - Card backgrounds
- `text-primary` / `text-secondary` - Text colors
- `border` / `border-dark` - Borders
- `hover:bg-hover` - Hover states
- `dark:` prefix for dark mode variants
