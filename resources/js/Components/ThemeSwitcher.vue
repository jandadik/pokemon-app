<template>
  <div class="relative">
    <button
      type="button"
      class="btn-icon"
      :title="getThemeLabel(userStore.getTheme)"
      @click="toggleMenu"
      ref="buttonRef"
    >
      <span :class="['mdi text-xl', getThemeIcon(userStore.getTheme)]"></span>
    </button>

    <Menu
      ref="menuRef"
      :model="menuItems"
      :popup="true"
      :pt="{
        root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-dropdown border border-border dark:border-border-dark min-w-[200px]',
        list: 'py-1',
        item: 'px-0',
        itemContent: 'px-3 py-2 hover:bg-hover dark:hover:bg-hover-dark cursor-pointer',
        itemLink: 'flex items-center gap-2 text-text-primary dark:text-text-primary-dark',
        separator: 'border-t border-border dark:border-border-dark my-1'
      }"
    >
      <template #start>
        <div class="px-3 py-2 text-xs font-semibold text-text-secondary dark:text-text-secondary-dark uppercase tracking-wider">
          Téma aplikace
        </div>
      </template>
      <template #item="{ item }">
        <div
          class="flex items-center justify-between w-full px-3 py-2 hover:bg-hover dark:hover:bg-hover-dark cursor-pointer"
          @click="changeTheme(item.value)"
        >
          <div class="flex items-center gap-2">
            <span :class="['mdi', item.icon]"></span>
            <span class="text-text-primary dark:text-text-primary-dark">{{ item.label }}</span>
          </div>
          <span
            v-if="userStore.getTheme === item.value"
            class="mdi mdi-check text-primary"
          ></span>
        </div>
      </template>
    </Menu>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/userStore'
import Menu from 'primevue/menu'

const menuRef = ref(null)
const buttonRef = ref(null)
const userStore = useUserStore()

const menuItems = ref([
  {
    label: 'Světlý režim',
    value: 'light',
    icon: 'mdi-white-balance-sunny'
  },
  {
    label: 'Tmavý režim',
    value: 'dark',
    icon: 'mdi-moon-waning-crescent'
  },
  {
    label: 'Podle systému',
    value: 'system',
    icon: 'mdi-theme-light-dark'
  }
])

const toggleMenu = (event) => {
  menuRef.value.toggle(event)
}

function getThemeLabel(theme) {
  const found = menuItems.value.find(t => t.value === theme)
  return found ? found.label : 'Nastavení tématu'
}

function getThemeIcon(theme) {
  if (theme === 'light') return 'mdi-white-balance-sunny'
  if (theme === 'dark') return 'mdi-moon-waning-crescent'
  return 'mdi-theme-light-dark'
}

function changeTheme(theme) {
  userStore.updateParameters({ theme })
  menuRef.value.hide()
}
</script>
