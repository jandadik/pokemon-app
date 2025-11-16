<template>
  <div class="relative inline-block">
    <button
      type="button"
      class="btn-text flex items-center gap-2 px-2"
      @click="toggleMenu"
      ref="buttonRef"
    >
      <span class="mdi mdi-translate"></span>
      <span class="hidden sm:inline">{{ currentLanguageLabel }}</span>
    </button>

    <Menu
      ref="menuRef"
      :model="menuItems"
      :popup="true"
      :pt="{
        root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-dropdown border border-border dark:border-border-dark',
        list: 'py-1',
        item: 'px-0',
        itemContent: 'hover:bg-hover dark:hover:bg-hover-dark cursor-pointer',
        itemLink: 'flex items-center text-text-primary dark:text-text-primary-dark'
      }"
    >
      <template #item="{ item }">
        <div
          :class="[
            'px-4 py-2 hover:bg-hover dark:hover:bg-hover-dark cursor-pointer',
            isActive(item.code) ? 'bg-primary/10 text-primary' : 'text-text-primary dark:text-text-primary-dark'
          ]"
          @click="switchLanguage(item.code)"
        >
          {{ item.label }}
        </div>
      </template>
    </Menu>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import axios from 'axios'
import Menu from 'primevue/menu'

const props = defineProps({
  location: {
    type: String,
    default: 'header',
  },
})

const page = usePage()
const userStore = useUserStore()
const menuRef = ref(null)
const buttonRef = ref(null)

const availableLanguages = [
  { code: 'cs', label: 'CS' },
  { code: 'en', label: 'EN' }
]

const menuItems = ref(availableLanguages)

const currentLanguage = computed(() => {
  if (page.props.user) {
    return userStore.getLanguage
  } else {
    return page.props.locale || 'cs'
  }
})

const currentLanguageLabel = computed(() => {
  const lang = availableLanguages.find(l => l.code === currentLanguage.value)
  return lang ? lang.label : 'CS'
})

const isActive = (code) => {
  return code === currentLanguage.value
}

const toggleMenu = (event) => {
  menuRef.value.toggle(event)
}

const switchLanguage = async (code) => {
  if (code === currentLanguage.value) return

  if (page.props.user) {
    userStore.updateLanguage(code)
  } else {
    try {
      await axios.get(route('public.language.switch', code))
      window.location.reload()
    } catch (error) {
      console.error('Error switching language:', error)
    }
  }
  menuRef.value.hide()
}
</script>
