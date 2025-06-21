# 🌍 Localization Automation System

Tento systém zajišťuje konzistentní a správné používání lokalizace v celém projektu, aby se předešlo hodinovému hledání chyb jako v `CollectionItemsTable.vue`.

## 🚀 Quick Start - Pro nové komponenty

### 1. Před kódováním - ověř klíče
```bash
# Zkontroluj, zda klíče existují
node scripts/check-localization-keys.js collections.fields.name ui.buttons.cancel

# Zobraz všechny ověřené klíče
node scripts/check-localization-keys.js
```

### 2. Použij snippets v VS Code
- `vue-i18n-component` - celá komponenta s lokalizací
- `vue-table-headers` - hlavičky tabulky s ověřenými klíči
- `vue-dialog-i18n` - dialog s UI lokalizací
- `vue-empty-state` - prázdný stav s lokalizací

### 3. Následuj pravidla
- **VŽDY** používej pouze `$t()` v template
- **NIKDY** neimportuj `useI18n`
- **OVĚŘ** klíče před použitím
- **NÁSLEDUJ** vzory z `Index.vue`

## 🛡️ Automatické kontroly

### ESLint pravidla (`.eslintrc.js`)
```javascript
// Automaticky zastaví build při použití zakázaných importů
'no-restricted-imports': [
  'error',
  {
    paths: [
      {
        name: 'vue-i18n',
        importNames: ['useI18n'],
        message: 'Forbidden: Use global $t() function instead of useI18n import.'
      }
    ]
  }
]
```

### Cursor Rules (`.cursor/rules/localization.mdc`)
- Kompletní pravidla s ověřenými klíči
- Příklady správného použití
- Seznam zakázaných vzorů
- Reference na `Index.vue`

## 📋 Ověřené klíče (vždy aktuální)

### ✅ Table Headers
```vue
<th>{{ $t('collections.fields.name') }}</th>
<th>{{ $t('collections.fields.actions') }}</th>
<th>{{ $t('collections.cards.table.condition') }}</th>
<th>{{ $t('collections.cards.table.quantity') }}</th>
<th>{{ $t('collections.form.language') }}</th>
```

### ✅ Buttons & Actions
```vue
<v-btn :title="$t('collections.buttons.edit')">Edit</v-btn>
<v-btn :title="$t('collections.buttons.delete')">Delete</v-btn>
<v-btn>{{ $t('ui.buttons.cancel') }}</v-btn>
<v-btn>{{ $t('ui.buttons.delete') }}</v-btn>
```

### ✅ Empty States
```vue
<!-- Pro obecné kolekce -->
<div>{{ $t('collections.empty.title') }}</div>
<div>{{ $t('collections.empty.text') }}</div>

<!-- Pro položky v kolekcích -->
<div>{{ $t('collections.items.empty_title') }}</div>
<div>{{ $t('collections.items.empty_text') }}</div>
```

### ✅ Dialogs
```vue
<v-card-title>{{ $t('ui.dialogs.delete') }}</v-card-title>
<v-card-text>{{ $t('ui.messages.confirm_delete') }}</v-card-text>
<div>{{ $t('ui.messages.action_irreversible') }}</div>
```

## ❌ Zakázané vzory

### NIKDY nepoužívej
```vue
<!-- ❌ Import useI18n -->
<script setup>
import { useI18n } from 'vue-i18n';  // FORBIDDEN!
const { t } = useI18n();              // FORBIDDEN!
</script>

<!-- ❌ Neexistující klíče -->
{{ $t('collections.fields.condition') }}   <!-- Použij collections.form.condition -->
{{ $t('collections.fields.quantity') }}    <!-- Použij collections.cards.table.quantity -->
{{ $t('collections.fields.language') }}    <!-- Použij collections.form.language -->

<!-- ❌ Vlastní helpery -->
{{ $trans('...') }}                         <!-- Použij $t() -->
```

## ✅ Správný vzor pro computed properties

```vue
<script setup>
import { ref, computed, getCurrentInstance } from 'vue';

// Získání globální $t() funkce
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

const headers = computed(() => [
  { title: $t('collections.fields.name'), key: 'name' },
  { title: $t('collections.cards.table.condition'), key: 'condition' },
  { title: $t('collections.form.language'), key: 'language' },
  { title: $t('collections.cards.table.quantity'), key: 'quantity' },
  { title: $t('collections.fields.actions'), key: 'actions' },
]);
</script>
```

## 🔧 Nástroje a skripty

### 1. Key Checker Script
```bash
# Ověř konkrétní klíče
node scripts/check-localization-keys.js collections.fields.name ui.buttons.cancel

# Zobraz help a všechny ověřené klíče
node scripts/check-localization-keys.js
```

### 2. VS Code Snippets
- **Lokace:** `.vscode/vue-localization.code-snippets`
- **Aktivace:** Začni psát `vue-i18n-` nebo `vue-table-` nebo `vue-dialog-`

### 3. Reference komponenta
- **Soubor:** `resources/js/Pages/Collections/Index.vue`
- **Použití:** Vždy se podívej, jak je tam řešena lokalizace

## 🔄 Workflow pro nové komponenty

1. **Plánování**
   ```bash
   # Ověř klíče, které chceš použít
   node scripts/check-localization-keys.js collections.fields.name
   ```

2. **Kódování**
   ```vue
   <!-- Použij snippet vue-i18n-component pro rychlý start -->
   <!-- V template používej pouze $t() -->
   <h1>{{ $t('collections.fields.name') }}</h1>
   ```

3. **Computed properties**
   ```javascript
   // Získej $t přes getCurrentInstance
   const instance = getCurrentInstance();
   const $t = instance.appContext.config.globalProperties.$t;
   ```

4. **Ověření**
   ```bash
   # ESLint automaticky zkontroluje zakázané importy
   npm run lint
   ```

## 🚨 Troubleshooting

### "Klíč se nezobrazuje" 
```bash
# Ověř, že klíč existuje
node scripts/check-localization-keys.js your.key.here

# Podívej se do překladového souboru
grep -r "your_key" resources/lang/cs/
```

### "ESLint error o useI18n"
```javascript
// ❌ Špatně
import { useI18n } from 'vue-i18n';

// ✅ Správně
import { getCurrentInstance } from 'vue';
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;
```

### "Computed property nefunguje"
```javascript
// ✅ Správný vzor
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

const headers = computed(() => [
  { title: $t('verified.key.here'), key: 'value' },
]);
```

## 📚 Reference dokumenty

- **Pravidla:** `.cursor/rules/localization.mdc`
- **Snippets:** `.vscode/vue-localization.code-snippets`
- **ESLint:** `.eslintrc.js` (sekce localization rules)
- **Vzorová komponenta:** `resources/js/Pages/Collections/Index.vue`
- **Checker script:** `scripts/check-localization-keys.js`

---

**🎯 Cíl:** Nikdy víc nehledat hodinu chyby v lokalizaci!

**💡 Zlaté pravidlo:** Pokud si nejsi jistý, podívej se do `Index.vue` a použij checker script. 