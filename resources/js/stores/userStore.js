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
        login_notifications: true,
        two_factor_enabled: false
      }
    },
    isLoading: false,
    isInitialized: false,
    loginHistory: []
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
    getLoginNotifications: (state) => state.parameters.settings.login_notifications,
    getTwoFactorEnabled: (state) => state.parameters.settings.two_factor_enabled,
    getLoginHistory: (state) => state.loginHistory
  },

  actions: {
    // Načtení parametrů z databáze
    async fetchParameters() {
      try {
        this.isLoading = true
        
        const response = await axios.get(route('parameters.fetch'))
        
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
      if (userParameters?.settings) {
        const parsedSettings = JSON.parse(userParameters.settings)
        
        this.parameters.settings = {
          ...this.parameters.settings,
          ...parsedSettings
        }
      }
    },

    // Aktualizace parametrů
    async updateParameters(newSettings) {
      try {
        this.isLoading = true
        
        // Aktualizujeme lokální stav
        this.parameters.settings = {
          ...this.parameters.settings,
          ...newSettings
        }

        // Odešleme na server
        await router.put(route('settings.update'), {
          ...newSettings
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
          },
          onError: () => {
            // V případě chyby vrátíme původní hodnoty
            this.parameters.settings = {
              ...this.parameters.settings,
              ...userParameters.settings
            }
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
        
        // Aktualizujeme lokální stav
        this.parameters.settings = {
          ...this.parameters.settings,
          ...notifications
        }

        // Odešleme na server
        await router.put(route('notifications.update'), {
          ...notifications
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
          },
          onError: () => {
            // V případě chyby vrátíme původní hodnoty
            this.parameters.settings = {
              ...this.parameters.settings,
              ...userParameters.settings
            }
          }
        })
      } finally {
        this.isLoading = false
      }
    },
    // Aktualizace notifikací
    async updateSecurity(security) {
      try {
        this.isLoading = true
        
        // Aktualizujeme lokální stav
        this.parameters.settings = {
          ...this.parameters.settings,
          ...security
        }

        // Odešleme na server
        await router.put(route('security.update'), {
          ...security
        }, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
          },
          onError: () => {
            // V případě chyby vrátíme původní hodnoty
            this.parameters.settings = {
              ...this.parameters.settings,
              ...userParameters.settings
            }
          }
        })
      } finally {
        this.isLoading = false
      }
    },
    async fetchLoginHistory() {
      try {
        this.isLoading = true;
        
        const response = await axios.get(route('login-history.index'));
        
        if (response.data && response.data.history) {
          this.loginHistory = response.data.history;
        } else {
          console.error('Neplatná odpověď serveru - chybí data historie:', response.data);
          this.loginHistory = [];
        }
        
        return this.loginHistory;
      } catch (error) {
        console.error('Chyba při načítání historie přihlášení:', error);
        this.loginHistory = [];
        return [];
      } finally {
        this.isLoading = false;
      }
    }
  }
})

// Automatická inicializace store při přihlášení
document.addEventListener('DOMContentLoaded', () => {
  const store = useUserStore()
  if (!store.isInitialized && document.querySelector('meta[name="user-id"]')) {
    store.fetchParameters()
  }
}) 