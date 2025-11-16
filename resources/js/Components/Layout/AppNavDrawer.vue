<template>
  <Sidebar
    v-model:visible="drawerVisible"
    :position="isMobile ? 'bottom' : 'left'"
    :pt="{
      root: 'bg-surface dark:bg-surface-dark',
      header: 'hidden',
      content: 'p-0'
    }"
  >
    <div class="flex flex-col h-full w-72">
      <!-- User profile section -->
      <Link
        v-if="auth.isLoggedIn"
        href="/profile"
        @click="closeDrawer"
        class="block p-4 hover:bg-hover dark:hover:bg-hover-dark"
      >
        <div class="flex items-center gap-3">
          <img
            src="https://randomuser.me/api/portraits/men/85.jpg"
            alt="User"
            class="w-10 h-10 rounded-full"
          />
          <span class="font-medium text-text-primary dark:text-text-primary-dark">
            {{ auth.user.name }}
          </span>
        </div>
      </Link>

      <div class="border-t border-border dark:border-border-dark"></div>

      <!-- Navigation links -->
      <nav class="flex-1 py-2">
        <Link
          v-if="auth.isLoggedIn && auth.can('admin.access')"
          href="/admin"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
        >
          <span class="mdi mdi-cog text-xl"></span>
          <span>Administrace</span>
        </Link>

        <Link
          href="/"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
        >
          <span class="mdi mdi-view-dashboard text-xl"></span>
          <span>Dashboard</span>
        </Link>

        <Link
          href="/collections/demo/card-variant-selection"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
        >
          <span class="mdi mdi-test-tube text-xl"></span>
          <span>Demo: Card Variant Selection</span>
        </Link>
      </nav>

      <div class="border-t border-border dark:border-border-dark"></div>

      <!-- Auth actions -->
      <div v-if="!auth.isLoggedIn" class="py-2">
        <Link
          href="/user-account/create"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
        >
          <span class="mdi mdi-account-plus text-xl"></span>
          <span>Registrovat</span>
        </Link>
        <Link
          href="/login"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
        >
          <span class="mdi mdi-login text-xl"></span>
          <span>Přihlásit</span>
        </Link>
      </div>

      <div v-if="auth.isLoggedIn" class="py-2">
        <Link
          href="/logout"
          method="delete"
          as="button"
          @click="closeDrawer"
          class="flex items-center gap-3 px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark w-full text-left"
        >
          <span class="mdi mdi-logout text-xl"></span>
          <span>Odhlásit</span>
        </Link>
      </div>

      <div class="border-t border-border dark:border-border-dark"></div>

      <!-- Settings section -->
      <div class="py-2">
        <div class="px-4 py-2 text-xs font-semibold text-text-secondary dark:text-text-secondary-dark uppercase tracking-wider">
          Nastavení
        </div>
        <div class="flex items-center justify-between px-4 py-3">
          <div class="flex items-center gap-3 text-text-primary dark:text-text-primary-dark">
            <span class="mdi mdi-theme-light-dark text-xl"></span>
            <span>Téma aplikace</span>
          </div>
          <ThemeSwitcher />
        </div>
      </div>
    </div>
  </Sidebar>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
import Sidebar from 'primevue/sidebar'

const auth = useAuthStore()
const isMobile = ref(false)

const props = defineProps({
  visible: Boolean,
})

const emit = defineEmits(['update:visible'])

const drawerVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

const closeDrawer = () => {
  emit('update:visible', false)
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})
</script>
