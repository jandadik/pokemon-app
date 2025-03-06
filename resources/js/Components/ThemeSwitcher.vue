<template>
  <div>
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      location="bottom"
    >
      <template v-slot:activator="{ props }">
        <v-btn
          icon
          v-bind="props"
          :title="getThemeLabel(userStore.getTheme)"
        >
          <v-icon>{{ getThemeIcon(userStore.getTheme) }}</v-icon>
        </v-btn>
      </template>
      
      <v-list density="compact" width="200">
        <v-list-subheader>Téma aplikace</v-list-subheader>
        
        <v-list-item
          v-for="(item, i) in themes"
          :key="i"
          :value="item.value"
          @click="changeTheme(item.value)"
        >
          <template v-slot:prepend>
            <v-icon :icon="item.icon" class="me-2"></v-icon>
          </template>
          
          <v-list-item-title>{{ item.title }}</v-list-item-title>
          
          <template v-slot:append>
            <v-icon
              v-if="userStore.getTheme === item.value"
              color="primary"
              icon="mdi-check"
            ></v-icon>
          </template>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/userStore'

const menu = ref(false)
const userStore = useUserStore()

const themes = [
  { 
    title: 'Světlý režim', 
    value: 'light', 
    icon: 'mdi-white-balance-sunny'
  },
  { 
    title: 'Tmavý režim', 
    value: 'dark', 
    icon: 'mdi-moon-waning-crescent'
  },
  { 
    title: 'Podle systému', 
    value: 'system', 
    icon: 'mdi-theme-light-dark'
  }
]

// Vrátí správný popis tématu
function getThemeLabel(theme) {
  const found = themes.find(t => t.value === theme)
  return found ? found.title : 'Nastavení tématu'
}

// Vrátí správnou ikonu pro téma
function getThemeIcon(theme) {
  if (theme === 'light') return 'mdi-white-balance-sunny'
  if (theme === 'dark') return 'mdi-moon-waning-crescent'
  return 'mdi-theme-light-dark'
}

// Změna tématu
function changeTheme(theme) {
  userStore.updateParameters({ theme })
  menu.value = false
}
</script> 