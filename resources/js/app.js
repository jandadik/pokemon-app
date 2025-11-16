import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

// i18n
import { setupI18n, TranslationPlugin } from '@/i18n'

// PrimeVue
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import Tooltip from 'primevue/tooltip'

// CSS
import '../css/app.css'
import '@mdi/font/css/materialdesignicons.css'
import 'primeicons/primeicons.css'

// Stores
import { useUserStore, initializeUserStore } from '@/stores/userStore'
import { watch } from 'vue'

// Pinia
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

// Detect system preference for dark mode
let prefersDarkTheme = false
if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
  prefersDarkTheme = true
}

// Apply initial theme class
if (prefersDarkTheme) {
  document.documentElement.classList.add('dark')
}

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || MainLayout

    return page
  },
  setup({ el, App, props, plugin }) {
    // Initialize i18n with translations from backend
    const locale = props.initialPage.props.locale || 'cs'
    const i18n = setupI18n(props.initialPage.props.translations || {}, locale)

    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(PrimeVue, {
        unstyled: true,
        pt: {} // Pass-through for custom styling
      })
      .use(ToastService)
      .use(ConfirmationService)
      .use(ZiggyVue)
      .use(i18n)
      .use(TranslationPlugin)

    // Register global directives
    app.directive('tooltip', Tooltip)

    // Set html lang attribute
    document.documentElement.lang = props.initialPage.props.locale || 'cs'

    app.mount(el)

    // Theme management
    const userStore = useUserStore()

    // System preference detection
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)')

    // Update theme function
    const updateTheme = () => {
      console.log('Updating theme, current setting:', userStore.getTheme)
      const themePreference = userStore.getTheme

      let isDark = false
      if (themePreference === 'system') {
        isDark = systemPrefersDark.matches
        console.log('Setting theme based on system:', isDark ? 'dark' : 'light')
      } else {
        isDark = themePreference === 'dark'
        console.log('Setting explicit theme:', themePreference)
      }

      // Apply dark class to html element for Tailwind
      if (isDark) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }

    // Load user parameters before setting theme
    if (document.querySelector('meta[name="user-id"]') && !userStore.isInitialized) {
      console.log('Loading parameters before setting theme')
      userStore.fetchParameters().then(() => {
        console.log('Parameters loaded, setting theme')
        updateTheme()
      })
    } else {
      console.log('Using current settings for theme')
      updateTheme()
    }

    // Watch for theme setting changes
    watch(() => userStore.getTheme, (newTheme) => {
      console.log('Theme changed to:', newTheme)
      updateTheme()
    })

    // Watch for system preference changes
    systemPrefersDark.addEventListener('change', updateTheme)

    // Initialize user store
    initializeUserStore(userStore)
  },
})
