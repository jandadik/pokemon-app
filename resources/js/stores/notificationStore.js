import { defineStore } from 'pinia'

export const useNotificationStore = defineStore('notification', {
  state: () => ({
    snackbars: []
  }),

  actions: {
    /**
     * Zobrazí toast notifikaci
     * @param {Object} notification - Notifikace
     * @param {string} notification.message - Text zprávy
     * @param {string} notification.type - Typ notifikace: 'success', 'error', 'warning', 'info'
     * @param {number} notification.timeout - Timeout v ms (default: 4000)
     * @param {boolean} notification.closable - Zda lze zavřít (default: true)
     */
    show(notification) {
      const id = Date.now() + Math.random()
      
      const snackbar = {
        id,
        message: notification.message || '',
        type: notification.type || 'info',
        timeout: notification.timeout || 4000,
        closable: notification.closable !== false,
        show: true
      }

      this.snackbars.push(snackbar)

      // Auto-hide po timeout
      if (snackbar.timeout > 0) {
        setTimeout(() => {
          this.hide(id)
        }, snackbar.timeout)
      }

      return id
    },

    /**
     * Skryje konkrétní notifikaci
     * @param {number} id - ID notifikace
     */
    hide(id) {
      const index = this.snackbars.findIndex(s => s.id === id)
      if (index > -1) {
        this.snackbars.splice(index, 1)
      }
    },

    /**
     * Skryje všechny notifikace
     */
    hideAll() {
      this.snackbars = []
    },

    /**
     * Rychlé metody pro různé typy notifikací
     */
    success(message, options = {}) {
      return this.show({ ...options, message, type: 'success' })
    },

    error(message, options = {}) {
      return this.show({ ...options, message, type: 'error', timeout: 6000 })
    },

    warning(message, options = {}) {
      return this.show({ ...options, message, type: 'warning', timeout: 5000 })
    },

    info(message, options = {}) {
      return this.show({ ...options, message, type: 'info' })
    }
  }
}) 