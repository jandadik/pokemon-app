<template>
    <v-responsive>
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
                <slot></slot>
            </v-main>
        </v-app>
    </v-responsive>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AppHeader from '@/Components/Layout/AppHeader.vue'
import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
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
</script>
