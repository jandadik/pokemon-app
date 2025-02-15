import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

// Vuetify
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// Pinia
import { createPinia } from 'pinia'

const vuetify = createVuetify({
    components,
    directives,
    icons: {
    defaultSet: 'mdi', // This is already the default value - only for display purposes
    },
  })

const pinia = createPinia()

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || MainLayout

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(vuetify)
      .use(pinia)
      .mount(el)
  },
})