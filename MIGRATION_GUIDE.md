# Vuetify to PrimeVue + Tailwind Migration Guide

This document provides patterns and instructions for completing the migration of remaining Vue components.

## Migration Status

### Completed
- **Infrastructure**: package.json, vite.config.js, tailwind.config.js, postcss.config.js
- **Entry point**: resources/js/app.js (PrimeVue setup, theme management)
- **CSS**: resources/css/app.css (Tailwind base with custom components)
- **Layout**: MainLayout.vue
- **Layout Components**: AppHeader, AppNavDrawer, AppTabs, AppHeaderActions
- **UI Components**: ApplicationLogo, NotificationToasts, ThemeSwitcher, LanguageSwitcher
- **Pages**: Index/Index.vue, Auth/Login.vue, Collections/Index.vue

### Remaining (~70 files)
See the patterns below to migrate remaining components.

---

## Component Mapping Reference

### Layout Components
| Vuetify | Tailwind/PrimeVue |
|---------|-------------------|
| `v-app` | `<div class="min-h-screen bg-background dark:bg-background-dark">` |
| `v-main` | `<main class="pt-16">` |
| `v-container` | `<div class="container-fluid">` or `max-w-[1400px] mx-auto` |
| `v-row` | `<div class="flex flex-wrap">` or `grid grid-cols-12 gap-4` |
| `v-col cols="6"` | `<div class="w-full md:w-1/2">` or `col-span-6` |
| `v-spacer` | `<div class="flex-grow">` |

### Form Components
| Vuetify | PrimeVue |
|---------|----------|
| `v-text-field` | `InputText` with Tailwind classes |
| `v-textarea` | `Textarea` |
| `v-select` | `Dropdown` |
| `v-checkbox` | `Checkbox` |
| `v-switch` | `InputSwitch` |
| `v-radio-group` | `RadioButton` |
| `v-form` | `<form @submit.prevent="">` |
| `v-btn type="submit"` | `Button` or `<button class="btn-primary">` |

### Display Components
| Vuetify | PrimeVue/Tailwind |
|---------|-------------------|
| `v-card` | `<div class="card">` |
| `v-card-title` | `<h2 class="text-xl font-semibold mb-4">` |
| `v-card-text` | `<div class="...">` |
| `v-card-actions` | `<div class="flex gap-2">` |
| `v-table` | `<table class="w-full">` |
| `v-data-table` | `DataTable` from PrimeVue |
| `v-chip` | `<span class="px-2 py-1 rounded-full bg-primary/10 text-primary">` |
| `v-alert` | `<div class="p-4 rounded-lg bg-{color}/10 border border-{color}/20">` |
| `v-icon` | `<span class="mdi mdi-{icon}">` |

### Overlay Components
| Vuetify | PrimeVue |
|---------|----------|
| `v-dialog` | `Dialog` |
| `v-menu` | `Menu` |
| `v-tooltip` | `v-tooltip` directive |
| `v-snackbar` | `Toast` or custom notification |
| `v-overlay` | `<div class="fixed inset-0 bg-black/50">` |

### Navigation Components
| Vuetify | PrimeVue/Tailwind |
|---------|-------------------|
| `v-app-bar` | `<header class="fixed top-0 ...">` |
| `v-navigation-drawer` | `Sidebar` |
| `v-tabs` | `TabView` |
| `v-tab` | `TabPanel` |
| `v-list` | `Menu` or `<nav class="...">` |
| `v-list-item` | `<div class="flex items-center gap-3 px-4 py-3 hover:bg-hover">` |
| `v-pagination` | `Paginator` |
| `v-breadcrumbs` | `Breadcrumb` |

---

## Migration Patterns

### Pattern 1: Simple Button
**Before (Vuetify):**
```vue
<v-btn color="primary" @click="action">
  Click Me
</v-btn>
```

**After (Tailwind):**
```vue
<button class="btn-primary" @click="action">
  Click Me
</button>
```

### Pattern 2: Button with Icon
**Before:**
```vue
<v-btn icon="mdi-plus" color="primary" variant="tonal" />
```

**After:**
```vue
<button class="btn-icon bg-primary/10 text-primary">
  <span class="mdi mdi-plus"></span>
</button>
```

### Pattern 3: Text Field with Icon
**Before:**
```vue
<v-text-field
  v-model="email"
  label="Email"
  prepend-inner-icon="mdi-email"
  :error-messages="errors.email"
/>
```

**After:**
```vue
<div class="mb-4">
  <label class="label">Email</label>
  <div class="relative">
    <span class="absolute left-3 top-1/2 -translate-y-1/2 mdi mdi-email text-text-secondary"></span>
    <InputText
      v-model="email"
      :class="['input pl-10', errors.email ? 'input-error' : '']"
    />
  </div>
  <p v-if="errors.email" class="error-text">{{ errors.email }}</p>
</div>
```

### Pattern 4: Select/Dropdown
**Before:**
```vue
<v-select
  v-model="selected"
  :items="items"
  label="Choose"
/>
```

**After:**
```vue
<Dropdown
  v-model="selected"
  :options="items"
  optionLabel="name"
  optionValue="id"
  placeholder="Choose"
  :pt="{
    root: 'w-full',
    input: 'input',
    list: 'bg-surface dark:bg-surface-dark rounded-lg shadow-dropdown'
  }"
/>
```

### Pattern 5: Card with Actions
**Before:**
```vue
<v-card>
  <v-card-title>Title</v-card-title>
  <v-card-text>Content</v-card-text>
  <v-card-actions>
    <v-btn color="primary">Action</v-btn>
  </v-card-actions>
</v-card>
```

**After:**
```vue
<div class="card">
  <h2 class="text-xl font-semibold mb-4">Title</h2>
  <div class="mb-4">Content</div>
  <div class="flex gap-2">
    <button class="btn-primary">Action</button>
  </div>
</div>
```

### Pattern 6: Dialog
**Before:**
```vue
<v-dialog v-model="show" max-width="400">
  <v-card>
    <v-card-title>Title</v-card-title>
    <v-card-text>Content</v-card-text>
    <v-card-actions>
      <v-btn @click="show = false">Cancel</v-btn>
      <v-btn color="primary" @click="confirm">Confirm</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
```

**After:**
```vue
<Dialog
  v-model:visible="show"
  :modal="true"
  :pt="{
    root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-xl max-w-md w-full',
    header: 'p-4 border-b border-border dark:border-border-dark',
    content: 'p-4',
    footer: 'p-4 border-t border-border dark:border-border-dark'
  }"
>
  <template #header>
    <h3 class="text-lg font-semibold">Title</h3>
  </template>
  <div>Content</div>
  <template #footer>
    <div class="flex justify-end gap-2">
      <button class="btn-outline" @click="show = false">Cancel</button>
      <button class="btn-primary" @click="confirm">Confirm</button>
    </div>
  </template>
</Dialog>
```

### Pattern 7: Responsive Grid
**Before:**
```vue
<v-row>
  <v-col cols="12" md="6" lg="4">
    Content
  </v-col>
</v-row>
```

**After:**
```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <div>Content</div>
</div>
```

### Pattern 8: Data Table
**Before:**
```vue
<v-data-table :items="items" :headers="headers" />
```

**After:**
```vue
<DataTable
  :value="items"
  :pt="{
    root: 'border border-border dark:border-border-dark rounded-lg',
    header: 'bg-surface dark:bg-surface-dark',
    tbody: 'divide-y divide-border dark:divide-border-dark'
  }"
>
  <Column field="name" header="Name" />
  <Column field="value" header="Value" />
</DataTable>
```

### Pattern 9: Replacing Vuetify Display Helpers
**Before:**
```vue
<div class="d-none d-md-flex">Desktop only</div>
<div class="d-flex d-md-none">Mobile only</div>
```

**After:**
```vue
<div class="hidden md:flex">Desktop only</div>
<div class="flex md:hidden">Mobile only</div>
```

### Pattern 10: Icons
**Before:**
```vue
<v-icon>mdi-check</v-icon>
<v-icon color="primary" size="24">mdi-star</v-icon>
```

**After:**
```vue
<span class="mdi mdi-check"></span>
<span class="mdi mdi-star text-primary text-2xl"></span>
```

---

## Dark Mode Support

Always add dark mode variants:
```vue
<div class="bg-surface dark:bg-surface-dark text-text-primary dark:text-text-primary-dark">
```

---

## Custom CSS Classes (defined in app.css)

- `.btn` - Base button
- `.btn-primary` - Primary colored button
- `.btn-secondary` - Secondary button
- `.btn-success` - Success button
- `.btn-warning` - Warning button
- `.btn-error` - Error/danger button
- `.btn-outline` - Outlined button
- `.btn-text` - Text-only button
- `.btn-icon` - Icon button
- `.input` - Form input
- `.input-error` - Input with error state
- `.label` - Form label
- `.error-text` - Error message text
- `.helper-text` - Helper text
- `.card` - Card container
- `.container-fluid` - Full-width container

---

## PrimeVue Pass-Through (pt) Styling

Use the `:pt` prop to style unstyled PrimeVue components:

```vue
<Dialog
  :pt="{
    root: 'bg-surface dark:bg-surface-dark rounded-lg',
    header: 'p-4 border-b border-border',
    content: 'p-4',
    footer: 'p-4 border-t border-border'
  }"
/>
```

---

## Import Statements

When using PrimeVue components, import them:
```javascript
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Dialog from 'primevue/dialog'
import Menu from 'primevue/menu'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Paginator from 'primevue/paginator'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'
import Sidebar from 'primevue/sidebar'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
```

---

## Installation

After updating package.json, run:
```bash
npm install
```

Then start the dev server:
```bash
npm run dev
```

---

## Files to Migrate (by priority)

### High Priority (Core Features)
1. Collections pages: Create.vue, Edit.vue, Show.vue
2. Collections/Items pages: Create.vue, Edit.vue
3. Auth pages: ForgotPassword.vue, ResetPassword.vue, VerifyEmail.vue, TwoFactorChallenge.vue
4. Card pages: Index.vue, Show.vue
5. Set pages: Index.vue, Show.vue

### Medium Priority
6. Account components (tabs)
7. Card components (CardItem, CardGridView, etc.)
8. Collections components (CollectionItemsTable, filters, etc.)

### Lower Priority
9. Admin pages
10. User account pages
11. Remaining utility components
