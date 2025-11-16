<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-surface dark:bg-surface-dark shadow-md">
    <div class="header-container max-w-[1400px] mx-auto w-full flex items-center h-16 px-4">
      <!-- Left part - menu toggle -->
      <button
        type="button"
        class="btn-icon flex-shrink-0"
        @click="$emit('update:drawer', !drawer)"
      >
        <span class="mdi mdi-menu text-xl"></span>
      </button>

      <!-- Logo and title -->
      <Link href="/" class="ml-3 mr-2 flex items-center">
        <ApplicationLogo />
      </Link>
      <h1 class="text-lg font-semibold text-text-primary dark:text-text-primary-dark">
        {{ $trans('app.app_name') }}
      </h1>

      <div class="flex-grow"></div>

      <!-- Right part - actions -->
      <AppHeaderActions />
    </div>

    <!-- Bottom tabs -->
    <div v-if="showTabs" class="border-t border-border dark:border-border-dark">
      <div class="header-container max-w-[1400px] mx-auto w-full">
        <div class="flex justify-center">
          <AppTabs />
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue'
import AppHeaderActions from '@/Components/Layout/AppHeaderActions.vue'
import AppTabs from '@/Components/Layout/AppTabs.vue'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()

defineProps({
  drawer: Boolean,
  showTabs: {
    type: Boolean,
    default: true
  }
})

defineEmits(['update:drawer'])
</script>
