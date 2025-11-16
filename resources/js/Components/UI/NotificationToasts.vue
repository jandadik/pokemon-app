<template>
  <Toast
    position="top-right"
    :pt="{
      root: 'fixed top-4 right-4 z-[10000]',
      message: 'flex items-center gap-3',
      content: 'flex items-center gap-3 p-3',
      icon: 'text-xl',
      text: 'flex-grow',
      closeButton: 'ml-2'
    }"
  />
  <!-- Custom toast container for manual toasts -->
  <div class="fixed top-4 right-4 z-[10000] flex flex-col gap-2 pointer-events-none">
    <TransitionGroup name="toast">
      <div
        v-for="snackbar in snackbars"
        :key="snackbar.id"
        class="pointer-events-auto"
      >
        <div
          :class="[
            'flex items-center gap-3 p-4 rounded-lg shadow-lg min-w-[300px] max-w-[400px]',
            getToastClasses(snackbar.type)
          ]"
        >
          <span :class="['mdi text-xl', getSnackbarIcon(snackbar.type)]"></span>
          <div class="flex-grow text-sm">
            {{ snackbar.message }}
          </div>
          <button
            v-if="snackbar.closable"
            type="button"
            class="p-1 rounded hover:bg-black/10 dark:hover:bg-white/10"
            @click="hideSnackbar(snackbar.id)"
          >
            <span class="mdi mdi-close text-base"></span>
          </button>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useNotificationStore } from '@/stores/notificationStore'
import Toast from 'primevue/toast'

const notificationStore = useNotificationStore()

const snackbars = computed(() => notificationStore.snackbars)

const hideSnackbar = (id) => {
  notificationStore.hide(id)
}

const getToastClasses = (type) => {
  const classes = {
    success: 'bg-success text-white',
    error: 'bg-error text-white',
    warning: 'bg-warning text-white',
    info: 'bg-info text-white'
  }
  return classes[type] || 'bg-info text-white'
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
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
