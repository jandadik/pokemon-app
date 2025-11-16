<template>
  <div class="flex items-center gap-2">
    <!-- Desktop buttons -->
    <button
      type="button"
      class="hidden md:flex btn-text items-center gap-2"
    >
      <span class="mdi mdi-heart"></span>
      <span>{{ $trans('ui.header.favorites') }}</span>
    </button>
    <button
      type="button"
      class="hidden md:flex btn-text items-center gap-2"
    >
      <span class="mdi mdi-magnify"></span>
      <span>{{ $trans('ui.header.search') }}</span>
    </button>

    <!-- Language switcher - Desktop only -->
    <LanguageSwitcher class="hidden md:block" />

    <!-- Theme switcher - Desktop only -->
    <ThemeSwitcher class="hidden md:block" />

    <!-- Mobile menu -->
    <div class="md:hidden relative">
      <button
        type="button"
        class="btn-icon"
        @click="toggleMobileMenu"
      >
        <span class="mdi mdi-dots-vertical text-xl"></span>
      </button>

      <Menu
        ref="mobileMenuRef"
        :model="mobileMenuItems"
        :popup="true"
        :pt="{
          root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-dropdown border border-border dark:border-border-dark min-w-[220px]',
          list: 'py-1',
          item: 'px-0',
          separator: 'border-t border-border dark:border-border-dark my-1'
        }"
      >
        <template #item="{ item }">
          <div
            v-if="item.type === 'action'"
            class="px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark cursor-pointer text-text-primary dark:text-text-primary-dark flex items-center gap-3"
          >
            <span :class="['mdi', item.icon]"></span>
            <span>{{ item.label }}</span>
          </div>
          <div
            v-else-if="item.type === 'component'"
            class="px-4 py-3 flex items-center justify-between"
          >
            <div class="flex items-center gap-3 text-text-primary dark:text-text-primary-dark">
              <span :class="['mdi', item.icon]"></span>
              <span>{{ item.label }}</span>
            </div>
            <component :is="item.component" />
          </div>
        </template>
      </Menu>
    </div>

    <!-- User menu (logged in) -->
    <div v-if="auth.isLoggedIn" class="relative">
      <button
        type="button"
        class="btn-icon"
        @click="toggleUserMenu"
      >
        <img
          src="https://randomuser.me/api/portraits/men/85.jpg"
          alt="user"
          class="w-8 h-8 rounded-full"
        />
      </button>

      <Menu
        ref="userMenuRef"
        :model="userMenuItems"
        :popup="true"
        :pt="{
          root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-dropdown border border-border dark:border-border-dark min-w-[180px]',
          list: 'py-1',
          item: 'px-0',
          separator: 'border-t border-border dark:border-border-dark my-1'
        }"
      >
        <template #item="{ item }">
          <Link
            v-if="item.href"
            :href="item.href"
            :method="item.method || 'get'"
            :as="item.as || 'a'"
            class="block px-4 py-3 hover:bg-hover dark:hover:bg-hover-dark text-text-primary dark:text-text-primary-dark"
          >
            <div class="flex items-center gap-3">
              <span :class="['mdi', item.icon]"></span>
              <span>{{ item.label }}</span>
            </div>
          </Link>
        </template>
      </Menu>
    </div>

    <!-- Login button (not logged in) -->
    <template v-else>
      <Link :href="route('auth.login')">
        <button
          type="button"
          class="btn-text hidden md:flex items-center"
        >
          {{ $trans('ui.header.login') }}
        </button>
        <button
          type="button"
          class="btn-icon md:hidden"
        >
          <span class="mdi mdi-login text-xl"></span>
        </button>
      </Link>
    </template>
  </div>
</template>

<script setup>
import { ref, markRaw } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import Menu from 'primevue/menu'

const auth = useAuthStore()
const mobileMenuRef = ref(null)
const userMenuRef = ref(null)

const mobileMenuItems = ref([
  {
    type: 'action',
    label: 'Favorites',
    icon: 'mdi-heart'
  },
  {
    type: 'action',
    label: 'Search',
    icon: 'mdi-magnify'
  },
  { separator: true },
  {
    type: 'component',
    label: 'Language',
    icon: 'mdi-translate',
    component: markRaw(LanguageSwitcher)
  },
  {
    type: 'component',
    label: 'Theme',
    icon: 'mdi-theme-light-dark',
    component: markRaw(ThemeSwitcher)
  }
])

const userMenuItems = ref([
  {
    label: 'Profile',
    icon: 'mdi-account',
    href: route('user.profile')
  },
  {
    label: 'Settings',
    icon: 'mdi-cog',
    href: route('user.profile', { tab: 'settings' })
  },
  { separator: true },
  {
    label: 'Logout',
    icon: 'mdi-logout',
    href: route('auth.logout'),
    method: 'delete',
    as: 'button'
  }
])

const toggleMobileMenu = (event) => {
  mobileMenuRef.value.toggle(event)
}

const toggleUserMenu = (event) => {
  userMenuRef.value.toggle(event)
}
</script>
