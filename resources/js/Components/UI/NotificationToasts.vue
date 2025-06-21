<template>
  <div class="notification-toasts">
    <v-snackbar
      v-for="snackbar in snackbars"
      :key="snackbar.id"
      v-model="snackbar.show"
      :color="getSnackbarColor(snackbar.type)"
      :timeout="-1"
      location="top right"
      multi-line
      class="notification-toast"
      rounded="lg"
      elevation="8"
    >
      <div class="d-flex align-center">
        <v-icon 
          :icon="getSnackbarIcon(snackbar.type)" 
          class="me-3"
          size="20"
        />
        <div class="text-body-2 flex-grow-1">
          {{ snackbar.message }}
        </div>
        <v-btn
          v-if="snackbar.closable"
          icon
          size="small"
          variant="text"
          @click="hideSnackbar(snackbar.id)"
        >
          <v-icon size="16">mdi-close</v-icon>
        </v-btn>
      </div>
    </v-snackbar>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useNotificationStore } from '@/stores/notificationStore'

const notificationStore = useNotificationStore()

const snackbars = computed(() => notificationStore.snackbars)

const hideSnackbar = (id) => {
  notificationStore.hide(id)
}

const getSnackbarColor = (type) => {
  const colors = {
    success: 'success',
    error: 'error',
    warning: 'warning',
    info: 'info'
  }
  return colors[type] || 'info'
}

const getSnackbarIcon = (type) => {
  const icons = {
    success: 'mdi-check-circle',
    error: 'mdi-alert-circle',
    warning: 'mdi-alert',
    info: 'mdi-information'
  }
  return icons[type] || 'mdi-information'
}
</script>

<style scoped>
.notification-toasts {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 10000;
  pointer-events: none;
}

.notification-toast {
  pointer-events: auto;
  margin-bottom: 8px;
}

:deep(.v-snackbar__wrapper) {
  margin-bottom: 8px;
}
</style> 