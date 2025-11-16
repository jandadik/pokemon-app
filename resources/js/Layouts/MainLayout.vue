<template>
  <div class="min-h-screen bg-background dark:bg-background-dark">
    <!-- Navigation drawer -->
    <AppNavDrawer
      :visible="drawer"
      @update:visible="drawer = $event"
    />

    <!-- Header -->
    <AppHeader
      :drawer="drawer"
      @update:drawer="drawer = $event"
    />

    <!-- Main content -->
    <main class="pt-16">
      <div class="app-content max-w-[1400px] mx-auto w-full px-4">
        <slot></slot>
      </div>
    </main>

    <!-- Toast notifications -->
    <NotificationToasts />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import AppHeader from '@/Components/Layout/AppHeader.vue'
import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
import NotificationToasts from '@/Components/UI/NotificationToasts.vue'
import { usePage } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'

const drawer = ref(false)
const page = usePage()
const userStore = useUserStore()

const flashSuccess = computed(() => page.props.flash.success)

// Watch for flash messages from server
watch(() => page.props.flash, (flash) => {
  if (flash && flash.success === 'new_login') {
    userStore.fetchParameters()
  }
}, { immediate: true })
</script>
