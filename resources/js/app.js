import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// i18n
import { setupI18n, TranslationPlugin } from '@/i18n'

// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { useUserStore, initializeUserStore } from '@/stores/userStore'
import { ref, watch } from 'vue'

// Pinia
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

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
    // Inicializace i18n s překlady z backendu
    const locale = props.initialPage.props.locale || 'cs';
    const i18n = setupI18n(props.initialPage.props.translations || {}, locale);
    
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(vuetify)
      .use(ZiggyVue)
      .use(i18n)
      .use(TranslationPlugin)
    
    // Nastavení html lang atributu podle aktuálního jazyka
    document.documentElement.lang = props.initialPage.props.locale || 'cs'
    
    app.mount(el)

    // Sledování změn v theme nastavení
    const userStore = useUserStore()
    
    // Detekce preferencí systému
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    
    // Reaktivní nastavení témat
    const updateTheme = () => {
      console.log('Aktualizace tématu, aktuální nastavení:', userStore.getTheme);
      const themePreference = userStore.getTheme
      
      if (themePreference === 'system') {
        // Nastavení podle systému
        vuetify.theme.global.name.value = systemPrefersDark.matches ? 'dark' : 'light'
        console.log('Nastaveno téma podle systému:', vuetify.theme.global.name.value);
      } else {
        // Explicitní nastavení uživatelem
        vuetify.theme.global.name.value = themePreference
        console.log('Nastaveno explicitní téma:', themePreference);
      }
    }
    
    // Zajistíme, že uživatelské parametry jsou načteny před inicializací tématu
    if (document.querySelector('meta[name="user-id"]') && !userStore.isInitialized) {
      console.log('Načítám parametry před nastavením tématu');
      userStore.fetchParameters().then(() => {
        console.log('Parametry načteny, nastavuji téma');
        updateTheme();
      });
    } else {
      console.log('Použití aktuálních nastavení pro téma');
      updateTheme();
    }
    
    // Sledování změn nastavení tématu
    watch(() => userStore.getTheme, (newTheme) => {
      console.log('Změna tématu na:', newTheme);
      updateTheme();
    });

    // Sledování změn preferencí systému
    systemPrefersDark.addEventListener('change', updateTheme)

    // Initialize user store
    initializeUserStore(userStore)
  },
})