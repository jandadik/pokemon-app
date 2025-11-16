<template>
  <nav class="flex items-center gap-1 py-2">
    <Link href="/">
      <button class="btn-text px-4 py-2">
        {{ $t('ui.menu.home') }}
      </button>
    </Link>
    <Link href="/sets">
      <button class="btn-text px-4 py-2">
        {{ $t('ui.menu.catalog') }}
      </button>
    </Link>
    <Link href="/cards">
      <button class="btn-text px-4 py-2">
        {{ $t('ui.menu.cards') }}
      </button>
    </Link>
    <Link
      v-if="auth.isLoggedIn"
      href="/collections"
    >
      <button class="btn-text px-4 py-2">
        {{ $t('ui.menu.collections') }}
      </button>
    </Link>
    <Link
      v-if="auth.isLoggedIn && auth.can('admin.access')"
      href="/admin"
    >
      <button class="btn-text px-4 py-2">
        {{ $t('ui.menu.admin') }}
      </button>
    </Link>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
</script>
