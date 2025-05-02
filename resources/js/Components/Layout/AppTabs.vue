<template>
    <Link href="/">
        <v-btn>
            {{ $t('ui.menu.home') }}
        </v-btn>
    </Link>
    <Link href="/sets">
        <v-btn>
            {{ $t('ui.menu.catalog') }}
        </v-btn>
    </Link>
    <Link href="/cards">
        <v-btn>
            {{ $t('ui.menu.cards') }}
        </v-btn>
    </Link>
    <Link 
        v-if="auth.isLoggedIn"
        href="/collections">
        <v-btn>
            {{ $t('ui.menu.collections') }}
        </v-btn>
    </Link>
    <Link 
        v-if="auth.isLoggedIn && auth.can('admin.access')"
        href="/admin">
        <v-btn>
            {{ $t('ui.menu.admin') }}
        </v-btn>
    </Link>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import { useAuthStore } from '@/stores/authStore'
    
    const auth = useAuthStore()
</script>