<template>
    <!-- TODO: Add global Error Boundary for the entire application -->
    <!-- TODO: Implement network error recovery mechanisms -->
    <v-app :theme="theme">
        <!-- Levý drawer s menu -->
        <AppNavDrawer
            :model-value="drawer"
            @update:model-value="drawer = $event"
        />
        <!-- Hlavní AppBar s taby -->
        <AppHeader 
            :drawer="drawer"
            @update:drawer="drawer = $event"
        />
        <!-- Hlavní obsah -->
        <v-main>
            <v-layout class="app-content">
                <slot></slot>
            </v-layout>
        </v-main>
        
        <!-- Toast notifikace -->
        <NotificationToasts />
    </v-app>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AppHeader from '@/Components/Layout/AppHeader.vue'
import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
import NotificationToasts from '@/Components/UI/NotificationToasts.vue'
import { usePage, Link } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import { useTheme } from 'vuetify'

const drawer = ref(false)
const page = usePage()
const userStore = useUserStore()
const vuetifyTheme = useTheme()

const flashSuccess = computed(
    () => page.props.flash.success,
)

// Sledování nastavení tmavého režimu
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)')
const isDarkMode = ref(systemPrefersDark.matches)

// Získání aktuálního tématu
const theme = computed(() => {
    const themePreference = userStore.getTheme
    
    if (themePreference === 'system') {
        return isDarkMode.value ? 'dark' : 'light'
    } else {
        return themePreference
    }
})

// Sledování změn systémových preferencí
onMounted(() => {
    systemPrefersDark.addEventListener('change', (e) => {
        isDarkMode.value = e.matches
    })
})

// Aplikování tématu do Vuetify
watch(theme, (newTheme) => {
    vuetifyTheme.global.name.value = newTheme
}, { immediate: true })

// Sledujeme flash zprávy ze serveru
watch(() => page.props.flash, (flash) => {
  //console.log('Flash zprávy ze serveru:', flash);
  
  if (flash && flash.success === 'new_login') {
    //console.log('Detekována flash zpráva success s hodnotou new_login, spouštím fetchParameters');
    userStore.fetchParameters();
  } else {
    //console.log('Žádná odpovídající flash zpráva nebyla nalezena');
  }
}, { immediate: true });
</script>

<style>
/* Globální styl pro omezení šířky obsahu aplikace v hlavičce i obsahu */
.app-content {
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}

/* Tento styl lze aplikovat pro kontejnery jak v hlavičce tak v obsahu */
.header-container {
    max-width: 1400px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}
</style>
