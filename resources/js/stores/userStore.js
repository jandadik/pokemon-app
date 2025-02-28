import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    parameters: {
      settings: {
        // Notifikace
        email_notifications: true,
        push_notifications: false,
        newsletter: false,
        
        // Nastavení
        language: 'cs',
        theme: 'system',
        
        // Zabezpečení
        login_notifications: true
      }
    },
    isLoading: false,
    isInitialized: false
  }),

  getters: {
    // Notifikace
    getEmailNotifications: (state) => state.parameters.settings.email_notifications,
    getPushNotifications: (state) => state.parameters.settings.push_notifications,
    getNewsletter: (state) => state.parameters.settings.newsletter,
    
    // Nastavení
    getLanguage: (state) => state.parameters.settings.language,
    getTheme: (state) => state.parameters.settings.theme,
    
    // Zabezpečení
    getLoginNotifications: (state) => state.parameters.settings.login_notifications
  },

  actions: {
    // Načtení parametrů z databáze
    async fetchParameters() {
      try {
        // console.log('Načítám parametry z databáze...')
        this.isLoading = true
        
        const response = await axios.get(route('parameters.fetch'))
        // console.log('Načtené parametry z databáze:', response.data)
        
        if (response.data.parameters) {
          this.initializeParameters(response.data.parameters)
        }
        
        this.isInitialized = true
      } catch (error) {
        console.error('Chyba při načítání parametrů:', error)
      } finally {
        this.isLoading = false
      }
    },

    // Inicializace parametrů z backendu
    initializeParameters(userParameters) {
      // console.log('Inicializace parametrů - vstupní data:', userParameters)
      
      if (userParameters?.settings) {
        const parsedSettings = JSON.parse(userParameters.settings)
        // console.log('Parsovaná nastavení:', parsedSettings)
        
        this.parameters.settings = {
          ...this.parameters.settings,
          ...parsedSettings
        }
        
        // console.log('Finální nastavení:', this.parameters.settings)
      } else {
        // console.log('Žádná nastavení nebyla načtena')
      }
    },

    // Aktualizace parametrů
    async updateParameters(newSettings) {
      try {
        this.isLoading = true
        // console.log('updateParameters - Nová nastavení k uložení:', newSettings)
        // console.log('updateParameters - Současný stav store:', this.parameters.settings)
        
        // Aktualizujeme lokální stav
        this.parameters.settings = {
          ...this.parameters.settings,
          ...newSettings
        }
        // console.log('updateParameters - Stav store po lokální aktualizaci:', this.parameters.settings)

        // console.log('updateParameters - Odesílám data do databáze...')
        // Odešleme na server
        await router.put(route('settings.update'), {
          ...newSettings
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            // console.log('updateParameters - Data úspěšně uložena do databáze')
          },
          onError: () => {
            // console.error('updateParameters - Chyba při ukládání do databáze, vracím původní hodnoty')
            // V případě chyby vrátíme původní hodnoty
            this.parameters.settings = {
              ...this.parameters.settings,
              ...userParameters.settings
            }
            // console.log('updateParameters - Obnovený stav store:', this.parameters.settings)
          }
        })
      } finally {
        this.isLoading = false
      }
    },

    // Aktualizace notifikací
    async updateNotifications(notifications) {
      try {
        this.isLoading = true
        // console.log('updateNotifications - Nová nastavení notifikací:', notifications)
        // console.log('updateNotifications - Současný stav store:', this.parameters.settings)
        
        // Aktualizujeme lokální stav
        this.parameters.settings = {
          ...this.parameters.settings,
          ...notifications
        }
        // console.log('updateNotifications - Stav store po lokální aktualizaci:', this.parameters.settings)

        // console.log('updateNotifications - Odesílám data do databáze...')
        // Odešleme na server
        await router.put(route('notifications.update'), {
          ...notifications
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            // console.log('updateNotifications - Data úspěšně uložena do databáze')
          },
          onError: () => {
            // console.error('updateNotifications - Chyba při ukládání do databáze, vracím původní hodnoty')
            // V případě chyby vrátíme původní hodnoty
            this.parameters.settings = {
              ...this.parameters.settings,
              ...userParameters.settings
            }
            // console.log('updateNotifications - Obnovený stav store:', this.parameters.settings)
          }
        })
      } finally {
        this.isLoading = false
      }
    }
  }
})

// Automatická inicializace store při přihlášení
document.addEventListener('DOMContentLoaded', () => {
  const store = useUserStore()
  if (!store.isInitialized && document.querySelector('meta[name="user-id"]')) {
    // console.log('Uživatel je přihlášen, inicializuji parametry...')
    store.fetchParameters()
  }
}) 