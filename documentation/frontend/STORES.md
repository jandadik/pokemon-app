# Pinia Stores Documentation

This document provides comprehensive documentation of all Pinia state management stores in the Pokemon Card Collection application.

## Stores Overview

| Store | File | Pattern | Persistence | Primary Purpose |
|-------|------|---------|-------------|-----------------|
| `useUserStore` | `userStore.js` | Options API | Yes (plugin) | User settings and preferences |
| `useAuthStore` | `authStore.js` | Composition API | No (Inertia props) | Authentication state |
| `useCollectionsStore` | `collections.js` | Composition API | Yes (localStorage) | Collection UI state |
| `useCardStore` | `cardStore.js` | Composition API | Partial (localStorage) | Card catalog state |
| `useNotificationStore` | `notificationStore.js` | Options API | No (ephemeral) | Toast notifications |
| `useImageStore` | `imageStore.js` | Options API | Yes (localStorage) | Image caching |
| `useTabStore` | `tabStore.js` | Options API | No | Navigation tabs |

---

## User Store

**File:** `resources/js/stores/userStore.js`

**Store Name:** `user`

### Purpose
Manages user preferences, settings, and login history with persistence.

### State
```javascript
{
  parameters: {
    settings: {
      email_notifications: true,
      push_notifications: false,
      newsletter: false,
      language: 'cs',
      theme: 'system',           // 'light', 'dark', 'system'
      login_notifications: true,
      auto_save_to_default_collection: true
    }
  },
  isLoading: false,
  isInitialized: false,
  loginHistory: []
}
```

### Getters
```javascript
getEmailNotifications: (state) => state.parameters.settings.email_notifications
getPushNotifications: (state) => state.parameters.settings.push_notifications
getNewsletter: (state) => state.parameters.settings.newsletter
getLanguage: (state) => state.parameters.settings.language
getTheme: (state) => state.parameters.settings.theme
getLoginNotifications: (state) => state.parameters.settings.login_notifications
getLoginHistory: (state) => state.loginHistory
getAutoSaveToDefaultCollection: (state) => state.parameters.settings.auto_save_to_default_collection
```

### Actions

#### `fetchParameters()`
Loads user parameters from backend API.

```javascript
async fetchParameters() {
  this.isLoading = true
  const response = await axios.get(route('user.parameters.fetch'))
  this.initializeParameters(response.data)
  this.isLoading = false
}
```

#### `initializeParameters(userParameters)`
Parses and merges backend parameters with default values.

```javascript
initializeParameters(userParameters) {
  if (userParameters && userParameters.settings) {
    const parsed = JSON.parse(userParameters.settings)
    this.parameters.settings = { ...this.parameters.settings, ...parsed }
  }
  this.isInitialized = true
}
```

#### `updateParameters(newSettings)`
Updates all settings and syncs with backend.

```javascript
async updateParameters(newSettings) {
  this.parameters.settings = { ...this.parameters.settings, ...newSettings }
  await router.put(route('user.settings.update'), {
    settings: this.parameters.settings
  })
  // Reloads page if language changed
}
```

#### `updateNotifications(notifications)`
Updates notification preferences.

```javascript
async updateNotifications(notifications) {
  this.parameters.settings = { ...this.parameters.settings, ...notifications }
  await router.put(route('user.notifications.update'), notifications)
}
```

#### `updateSecurity(security)`
Updates security settings.

```javascript
async updateSecurity(security) {
  await router.put(route('user.security.update'), security)
}
```

#### `fetchLoginHistory()`
Fetches user's login history.

```javascript
async fetchLoginHistory() {
  const response = await axios.get(route('user.login-history.index'))
  this.loginHistory = response.data
}
```

#### `updateLanguage(language)`
Updates language and triggers page reload.

```javascript
async updateLanguage(language) {
  this.parameters.settings.language = language
  await router.visit(route('public.language.switch', { locale: language }))
  // Full page reload for translation updates
}
```

### Persistence
```javascript
persist: true  // Uses pinia-plugin-persistedstate
// Stores entire state in localStorage as 'user'
```

### Helper Function
```javascript
export function initializeUserStore(providedStore) {
  // Called on app startup
  // Checks for user meta tag and initializes store
  const userMeta = document.querySelector('meta[name="user-parameters"]')
  if (userMeta) {
    providedStore.initializeParameters(JSON.parse(userMeta.content))
  }
}
```

---

## Auth Store

**File:** `resources/js/stores/authStore.js`

**Store Name:** `auth`

### Purpose
Provides authentication state derived from Inertia page props. Read-only store.

### Computed State
```javascript
const page = usePage()

const user = computed(() => page.props.user)
const isLoggedIn = computed(() => !!user.value)
```

### Methods

#### `hasRole(role)`
Checks if user has specific role.

```javascript
const hasRole = (role) => {
  return user.value?.roles?.includes(role) ?? false
}
```

#### `can(permission)`
Checks if user has permission with 3-tier fallback.

```javascript
const can = (permission) => {
  // 1. Check direct permissions array
  if (user.value?.permissions?.includes(permission)) {
    return true
  }

  // 2. Check is_admin flag (grants all permissions)
  if (user.value?.is_admin) {
    return true
  }

  // 3. Check role-based permissions map
  const rolePermissions = {
    'super-admin': ['admin.access', 'user.create', 'user.edit', 'user.delete'],
    'admin': ['admin.access', 'user.create', 'user.edit', 'user.delete'],
    'editor': ['admin.access', 'user.edit'],
    'user': []
  }

  for (const role of user.value?.roles || []) {
    if (rolePermissions[role]?.includes(permission)) {
      return true
    }
  }

  return false
}
```

### Usage
```javascript
const auth = useAuthStore()

if (auth.isLoggedIn) {
  console.log('User:', auth.user.name)
}

if (auth.can('admin.access')) {
  // Show admin panel
}

if (auth.hasRole('super-admin')) {
  // Super admin features
}
```

### Persistence
None - derives state from Inertia props (server-side source of truth).

---

## Collections Store

**File:** `resources/js/stores/collections.js`

**Store Name:** `collections`

### Purpose
Manages collection UI state: view mode, filters, pagination, and bulk selection.

### State
```javascript
const viewMode = ref('grid')           // 'grid' or 'list'
const filters = ref({})                 // Active filter values
const perPage = ref(30)                 // Items per page
const showStats = ref(false)            // Show statistics panel
const selectedItems = ref(new Set())   // Selected item IDs
const bulkMode = ref(false)             // Bulk selection mode active
const onFiltersChangeCallback = ref(null)  // Filter change callback
```

### Computed Getters

#### `hasActiveFilters`
```javascript
const hasActiveFilters = computed(() => {
  return Object.keys(filters.value).some(key => {
    if (key === 'page' || key === 'per_page') return false
    return filters.value[key] !== null && filters.value[key] !== ''
  })
})
```

#### `hasSelectedItems`
```javascript
const hasSelectedItems = computed(() => selectedItems.value.size > 0)
```

#### `selectedItemsCount`
```javascript
const selectedItemsCount = computed(() => selectedItems.value.size)
```

#### `selectedItemsArray`
```javascript
const selectedItemsArray = computed(() => Array.from(selectedItems.value))
```

### Actions

#### `setViewMode(mode)`
```javascript
const setViewMode = (mode) => {
  viewMode.value = mode
  saveToLocalStorage()
}
```

#### `setFilters(newFilters)`
```javascript
const setFilters = (newFilters) => {
  filters.value = { ...newFilters }
  if (onFiltersChangeCallback.value) {
    onFiltersChangeCallback.value(filters.value)
  }
}
```

#### `updateFilter(key, value)`
Updates single filter with debounced callback for search.

```javascript
const updateFilter = (key, value) => {
  filters.value[key] = value

  if (key === 'search' && onFiltersChangeCallback.value) {
    // Debounce search for 500ms
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
      onFiltersChangeCallback.value(filters.value)
    }, 500)
  } else if (onFiltersChangeCallback.value) {
    onFiltersChangeCallback.value(filters.value)
  }
}
```

#### `resetFilters()`
```javascript
const resetFilters = () => {
  filters.value = {}
  if (onFiltersChangeCallback.value) {
    onFiltersChangeCallback.value(filters.value)
  }
}
```

#### `toggleItemSelection(itemId)`
```javascript
const toggleItemSelection = (itemId) => {
  if (selectedItems.value.has(itemId)) {
    selectedItems.value.delete(itemId)
  } else {
    selectedItems.value.add(itemId)
  }
  // Auto-enable bulk mode if items selected
  if (selectedItems.value.size > 0) {
    bulkMode.value = true
  }
}
```

#### `selectAllItems(itemIds)`
```javascript
const selectAllItems = (itemIds) => {
  itemIds.forEach(id => selectedItems.value.add(id))
  bulkMode.value = true
}
```

#### `clearSelectedItems()`
```javascript
const clearSelectedItems = () => {
  selectedItems.value.clear()
  bulkMode.value = false
}
```

#### `isItemSelected(itemId)`
```javascript
const isItemSelected = (itemId) => selectedItems.value.has(itemId)
```

#### `saveToLocalStorage()`
```javascript
const saveToLocalStorage = () => {
  const data = {
    viewMode: viewMode.value,
    filters: filters.value,
    perPage: perPage.value,
    showStats: showStats.value
  }
  localStorage.setItem('collections_store', JSON.stringify(data))
}
```

#### `loadFromLocalStorage()`
```javascript
const loadFromLocalStorage = () => {
  const stored = localStorage.getItem('collections_store')
  if (stored) {
    const data = JSON.parse(stored)
    viewMode.value = data.viewMode || 'grid'
    filters.value = data.filters || {}
    perPage.value = data.perPage || 30
    showStats.value = data.showStats || false
  }
}
```

#### `initializeFromProps(propsFilters, propsPerPage)`
```javascript
const initializeFromProps = (propsFilters, propsPerPage) => {
  loadFromLocalStorage()
  if (propsFilters) {
    filters.value = { ...propsFilters }
  }
  if (propsPerPage) {
    perPage.value = propsPerPage
  }
}
```

### Persistence
Custom localStorage with JSON serialization. Persists:
- viewMode
- filters
- perPage
- showStats

Does NOT persist:
- selectedItems (ephemeral)
- bulkMode (ephemeral)

---

## Card Store

**File:** `resources/js/stores/cardStore.js`

**Store Name:** `cards`

### Purpose
Manages card catalog browsing state with server-side data fetching.

### State
```javascript
const search = ref('')
const type = ref('')
const rarity = ref('')
const set_id = ref('')
const sort_by = ref('name')
const sort_direction = ref('asc')
const per_page = ref(30)
const view_mode = ref('grid')
const total_cards = ref(0)
const isLoading = ref(false)
const currentPage = ref(1)
```

### Computed Getters

#### `hasActiveFilters`
```javascript
const hasActiveFilters = computed(() => {
  return search.value || type.value || rarity.value || set_id.value
})
```

#### `filters`
```javascript
const filters = computed(() => ({
  search: search.value,
  type: type.value,
  rarity: rarity.value,
  set_id: set_id.value,
  sort_by: sort_by.value,
  sort_direction: sort_direction.value,
  per_page: per_page.value,
  page: currentPage.value
}))
```

#### `sortOption` (getter/setter)
```javascript
const sortOption = computed({
  get: () => `${sort_by.value}_${sort_direction.value}`,
  set: (value) => {
    const [by, direction] = value.split('_')
    sort_by.value = by
    sort_direction.value = direction
  }
})
```

### Actions

#### `setSearch(value)`
```javascript
const setSearch = (value) => {
  search.value = value
  debouncedApplyFilters(true)  // Reset to page 1
}
```

#### `setType(value)`
```javascript
const setType = (value) => {
  type.value = value
  applyFilters(true)
}
```

#### `setSortOption(value)`
```javascript
const setSortOption = (value) => {
  sortOption.value = value
  applyFilters(false)  // Keep current page
}
```

#### `setViewMode(value)`
```javascript
const setViewMode = (value) => {
  view_mode.value = value
  localStorage.setItem('card_view_mode', value)
}
```

#### `resetFilters()`
```javascript
const resetFilters = () => {
  search.value = ''
  type.value = ''
  rarity.value = ''
  set_id.value = ''
  sort_by.value = 'name'
  sort_direction.value = 'asc'
  currentPage.value = 1
  applyFilters(false)
}
```

#### `applyFilters(resetPage = false)`
```javascript
const applyFilters = (resetPage = false) => {
  if (resetPage) {
    currentPage.value = 1
  }

  isLoading.value = true

  router.visit(route('catalog.cards.index'), {
    data: filters.value,
    preserveState: true,
    preserveScroll: false,
    onFinish: () => {
      isLoading.value = false
    }
  })
}
```

#### `debouncedApplyFilters`
```javascript
const debouncedApplyFilters = debounce((resetPage) => {
  applyFilters(resetPage)
}, 500)
```

#### `initializeFromProps(props)`
```javascript
const initializeFromProps = (props) => {
  if (props.filters) {
    search.value = props.filters.search || ''
    type.value = props.filters.type || ''
    rarity.value = props.filters.rarity || ''
    set_id.value = props.filters.set_id || ''
  }
  if (props.cards) {
    total_cards.value = props.cards.meta?.total || 0
    currentPage.value = props.cards.meta?.current_page || 1
  }
  initializeFromLocalStorage()
}
```

### Persistence
- `view_mode` - Stored in localStorage as `card_view_mode`
- Other filters are URL-based (server-side routing)

---

## Notification Store

**File:** `resources/js/stores/notificationStore.js`

**Store Name:** `notification`

### Purpose
Manages toast notifications with auto-dismiss and type-based styling.

### State
```javascript
{
  snackbars: []  // Array of notification objects
}
```

### Notification Object
```javascript
{
  id: 'unique_id',
  message: 'Success message',
  type: 'success',      // 'success', 'error', 'warning', 'info'
  timeout: 4000,        // Auto-dismiss in ms
  closable: true
}
```

### Actions

#### `show(notification)`
```javascript
show(notification) {
  const id = Date.now().toString()
  const snackbar = {
    id,
    message: notification.message,
    type: notification.type || 'info',
    timeout: notification.timeout || 4000,
    closable: notification.closable !== false
  }

  this.snackbars.push(snackbar)

  // Auto-dismiss after timeout
  if (snackbar.timeout > 0) {
    setTimeout(() => {
      this.hide(id)
    }, snackbar.timeout)
  }

  return id
}
```

#### `hide(id)`
```javascript
hide(id) {
  this.snackbars = this.snackbars.filter(s => s.id !== id)
}
```

#### `hideAll()`
```javascript
hideAll() {
  this.snackbars = []
}
```

#### Convenience Methods
```javascript
success(message, options = {}) {
  return this.show({ ...options, message, type: 'success', timeout: 4000 })
}

error(message, options = {}) {
  return this.show({ ...options, message, type: 'error', timeout: 6000 })
}

warning(message, options = {}) {
  return this.show({ ...options, message, type: 'warning', timeout: 5000 })
}

info(message, options = {}) {
  return this.show({ ...options, message, type: 'info', timeout: 4000 })
}
```

### Usage
```javascript
const notification = useNotificationStore()

notification.success('Collection created successfully!')
notification.error('Failed to delete item')
notification.warning('You have unsaved changes')
notification.info('New cards added to catalog')
```

### Persistence
None - notifications are ephemeral UI state.

---

## Image Store

**File:** `resources/js/stores/imageStore.js`

**Store Name:** `images`

### Purpose
Caches card images with TTL-based persistence and bulk loading optimization.

### State
```javascript
{
  imageCache: new Map(),        // cardId_size -> image data
  loadingImages: new Set(),     // Currently loading images
  bulkQueue: new Set(),         // Queued for bulk loading
  bulkTimer: null,              // Debounce timer
  cacheVersion: '1.0',          // Cache version for compatibility
  maxCacheSize: 1000,           // Max cached images
  initialized: false
}
```

### Getters

#### `getImageData(cardId, size)`
```javascript
getImageData: (state) => (cardId, size = 'small') => {
  const key = `${cardId}_${size}`
  return state.imageCache.get(key)
}
```

#### `isCached(cardId, size)`
```javascript
isCached: (state) => (cardId, size = 'small') => {
  return state.imageCache.has(`${cardId}_${size}`)
}
```

#### `isLoading(cardId, size)`
```javascript
isLoading: (state) => (cardId, size = 'small') => {
  return state.loadingImages.has(`${cardId}_${size}`)
}
```

#### `cacheStats`
```javascript
cacheStats: (state) => ({
  size: state.imageCache.size,
  loadingCount: state.loadingImages.size,
  usagePercent: (state.imageCache.size / state.maxCacheSize) * 100
})
```

### Actions

#### `init()`
```javascript
init() {
  if (!this.initialized) {
    this.loadPersistedCache()
    this.initialized = true
  }
}
```

#### `loadImageData(cardId, size)`
```javascript
async loadImageData(cardId, size = 'small') {
  const key = `${cardId}_${size}`

  // Return cached
  if (this.imageCache.has(key)) {
    return this.imageCache.get(key)
  }

  // Wait if already loading
  if (this.loadingImages.has(key)) {
    return await this.waitForLoading(key)
  }

  // Fetch new
  this.loadingImages.add(key)
  const data = await this.fetchImageData([cardId], size)
  this.loadingImages.delete(key)

  if (data[cardId]) {
    this.cacheImageData(key, data[cardId])
    return data[cardId]
  }

  return this.getPlaceholderData()
}
```

#### `loadBulkImageData(cardIds, size)`
```javascript
async loadBulkImageData(cardIds, size = 'small') {
  const uncached = cardIds.filter(id => !this.isCached(id, size))

  if (uncached.length === 0) {
    return cardIds.map(id => this.getImageData(id, size))
  }

  const data = await this.fetchImageData(uncached, size)

  // Cache all results
  Object.entries(data).forEach(([cardId, imageData]) => {
    this.cacheImageData(`${cardId}_${size}`, imageData)
  })

  return cardIds.map(id => this.getImageData(id, size) || this.getPlaceholderData())
}
```

#### `queueImageLoad(cardId, size)`
```javascript
queueImageLoad(cardId, size = 'small') {
  this.bulkQueue.add(`${cardId}_${size}`)

  // Debounce bulk processing (50ms)
  clearTimeout(this.bulkTimer)
  this.bulkTimer = setTimeout(() => {
    this.processBulkQueue()
  }, 50)
}
```

#### `processBulkQueue()`
```javascript
async processBulkQueue() {
  const queue = Array.from(this.bulkQueue)
  this.bulkQueue.clear()

  // Group by size
  const bySize = queue.reduce((acc, key) => {
    const [cardId, size] = key.split('_')
    if (!acc[size]) acc[size] = []
    acc[size].push(cardId)
    return acc
  }, {})

  // Fetch each size group
  for (const [size, cardIds] of Object.entries(bySize)) {
    await this.loadBulkImageData(cardIds, size)
  }
}
```

#### `fetchImageData(cardIds, size)`
```javascript
async fetchImageData(cardIds, size) {
  const response = await axios.post('/images/bulk', {
    cardIds,
    size
  })
  return response.data
}
```

#### `cacheImageData(key, imageData)`
```javascript
cacheImageData(key, imageData) {
  // FIFO eviction if at max size
  if (this.imageCache.size >= this.maxCacheSize) {
    const firstKey = this.imageCache.keys().next().value
    this.imageCache.delete(firstKey)
  }

  this.imageCache.set(key, {
    ...imageData,
    cachedAt: Date.now()
  })

  this.persistCache()
}
```

#### `persistCache()`
```javascript
persistCache() {
  try {
    const cacheData = {
      version: this.cacheVersion,
      timestamp: Date.now(),
      data: Object.fromEntries(this.imageCache)
    }

    const serialized = JSON.stringify(cacheData)

    // Max 5MB in localStorage
    if (serialized.length < 5 * 1024 * 1024) {
      localStorage.setItem('pokemon_image_cache', serialized)
    }
  } catch (e) {
    console.warn('Failed to persist image cache:', e)
  }
}
```

#### `loadPersistedCache()`
```javascript
loadPersistedCache() {
  try {
    const stored = localStorage.getItem('pokemon_image_cache')
    if (!stored) return

    const cacheData = JSON.parse(stored)

    // Check version compatibility
    if (cacheData.version !== this.cacheVersion) {
      localStorage.removeItem('pokemon_image_cache')
      return
    }

    // Check TTL (7 days)
    const maxAge = 7 * 24 * 60 * 60 * 1000
    if (Date.now() - cacheData.timestamp > maxAge) {
      localStorage.removeItem('pokemon_image_cache')
      return
    }

    // Restore cache
    this.imageCache = new Map(Object.entries(cacheData.data))
  } catch (e) {
    console.warn('Failed to load persisted cache:', e)
  }
}
```

#### `clearCache()`
```javascript
clearCache() {
  this.imageCache.clear()
  this.loadingImages.clear()
  this.bulkQueue.clear()
  localStorage.removeItem('pokemon_image_cache')
}
```

#### `getPlaceholderData()`
```javascript
getPlaceholderData() {
  return {
    url: '/images/placeholder-card.png',
    fallback: '/images/placeholder-card.png',
    isPlaceholder: true
  }
}
```

### Persistence
- Version-controlled localStorage
- 7-day TTL
- Max 5MB storage limit
- FIFO eviction at 1000 images

---

## Tab Store

**File:** `resources/js/stores/tabStore.js`

**Store Name:** `tab`

### Purpose
Manages navigation tab state (minimal implementation).

### State
```javascript
{
  activeTab: null,
  tabs: [
    { title: 'Home', value: 'dashboard', route: 'index' },
    { title: 'Katalog', value: 'sets', route: '/set' },
    { title: 'Admin', value: 'admin', route: 'hello' }
  ]
}
```

### Actions

#### `setActiveTab(value)`
```javascript
setActiveTab(value) {
  this.activeTab = value
}
```

### Persistence
None - tab state derived from current route.

---

## Store Initialization Pattern

### App Entry Point (`app.js`)
```javascript
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { initializeUserStore } from '@/stores/userStore'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

createApp({ render: () => h(App, props) })
  .use(pinia)
  .mount('#app')

// Initialize user store after mounting
const userStore = useUserStore()
initializeUserStore(userStore)
```

### Store Usage in Components
```javascript
import { useAuthStore } from '@/stores/authStore'
import { useCollectionsStore } from '@/stores/collections'

const auth = useAuthStore()
const collections = useCollectionsStore()

// Access state
const isLoggedIn = auth.isLoggedIn
const viewMode = collections.viewMode

// Call actions
collections.setViewMode('list')
```

---

## Best Practices

### 1. Separation of Concerns
- **Auth**: Server-derived (Inertia props)
- **User**: Persistent user preferences
- **UI**: View state (filters, modes)
- **Cache**: Performance optimization (images)
- **Notifications**: Ephemeral feedback

### 2. Persistence Strategy
- Use plugin for simple persistence (userStore)
- Custom localStorage for complex data (imageStore)
- URL-based state for shareable data (cardStore filters)

### 3. Reactivity
- Use `ref()` and `computed()` in Composition API stores
- State changes automatically trigger re-renders

### 4. Performance
- Debounce expensive operations (search, API calls)
- Cache frequently accessed data (images)
- Use TTL to prevent stale data
