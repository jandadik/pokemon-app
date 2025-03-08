<template>
  <div class="language-switcher">
    <v-menu location="bottom">
      <template v-slot:activator="{ props }">
        <v-btn
          v-bind="props"
          variant="text"
          class="px-2"
        >
          <v-icon>mdi-translate</v-icon>
          <span class="ml-2 d-none d-sm-inline">{{ currentLanguageLabel }}</span>
        </v-btn>
      </template>
      <v-list density="compact" class="py-0">
        <v-list-item
          v-for="lang in availableLanguages"
          :key="lang.code"
          :value="lang.code"
          :active="isActive(lang.code)"
          @click="switchLanguage(lang.code)"
        >
          <v-list-item-title>{{ lang.label }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import axios from 'axios'

const props = defineProps({
  // Umístění přepínače (např. 'header', 'settings')
  location: {
    type: String,
    default: 'header',
  },
})

const page = usePage()
const userStore = useUserStore()

// Seznam dostupných jazyků
const availableLanguages = [
  { code: 'cs', label: 'CS' },
  { code: 'en', label: 'EN' }
]

// Získání aktuálního jazyka
const currentLanguage = computed(() => {
  if (page.props.user) {
    // Přihlášený uživatel - bereme z userStore
    return userStore.getLanguage
  } else {
    // Nepřihlášený uživatel - bereme z props
    return page.props.locale || 'cs'
  }
})

// Popisek aktuálního jazyka
const currentLanguageLabel = computed(() => {
  const lang = availableLanguages.find(l => l.code === currentLanguage.value)
  return lang ? lang.label : 'Čeština'
})

// Kontrola, zda je jazyk aktivní
const isActive = (code) => {
  return code === currentLanguage.value
}

// Přepnutí jazyka
const switchLanguage = async (code) => {
  // Pokud se jazyk nezměnil, nic neděláme
  if (code === currentLanguage.value) return
  
  if (page.props.user) {
    // Pro přihlášeného uživatele - použijeme specifickou metodu v userStore
    userStore.updateLanguage(code)
  } else {
    // Pro nepřihlášeného uživatele - uložení do session a obnovení stránky
    try {
      await axios.get(route('public.language.switch', code))
      window.location.reload()
    } catch (error) {
      console.error('Chyba při přepínání jazyka:', error)
    }
  }
}
</script>

<style scoped>
.language-switcher {
  display: inline-block;
}
</style> 