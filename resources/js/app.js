import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { useUserStore } from '@/stores/userStore'
import { ref, watch } from 'vue'

// Pinia
import { createPinia } from 'pinia'

const pinia = createPinia()

// Vytvoříme defaultní hodnotu pro tmavý režim před inicializací Vuetify
let prefersDarkTheme = false

// Detekce nastavení systému
if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
  prefersDarkTheme = true
}

// Vytvoření instance Vuetify s podporou tmavého režimu
const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi', // This is already the default value - only for display purposes
  },
  theme: {
    defaultTheme: prefersDarkTheme ? 'dark' : 'light',
    themes: {
      light: {
        colors: {
          primary: '#1867C0',
          secondary: '#5CBBF6',
          accent: '#8c9eff',
          error: '#b71c1c',
        },
      },
      dark: {
        colors: {
          primary: '#2196F3',
          secondary: '#424242',
          accent: '#FF4081',
          error: '#FF5252',
        },
      },
    },
  },
})

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || MainLayout

    return page
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(vuetify)
      .use(ZiggyVue)
    
    app.mount(el)

    // Sledování změn v theme nastavení
    const userStore = useUserStore()
    
    // Detekce preferencí systému
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    
    // Reaktivní nastavení témat
    const updateTheme = () => {
      const themePreference = userStore.getTheme
      
      if (themePreference === 'system') {
        // Nastavení podle systému
        vuetify.theme.global.name.value = systemPrefersDark.matches ? 'dark' : 'light'
      } else {
        // Explicitní nastavení uživatelem
        vuetify.theme.global.name.value = themePreference
      }
    }
    
    // Sledování změn nastavení tématu
    watch(() => userStore.getTheme, updateTheme, { immediate: true })

    // Sledování změn preferencí systému
    systemPrefersDark.addEventListener('change', updateTheme)
  },
})