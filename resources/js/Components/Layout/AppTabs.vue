<template>
    <Link href="/">
        <v-btn>
            Home
        </v-btn>
    </Link>
    <Link href="/set">
        <v-btn>
            Katalog
        </v-btn>
    </Link>
    <Link href="/card">
        <v-btn>
            Karty
        </v-btn>
    </Link>
    <Link 
        v-if="auth.isLoggedIn && auth.can('admin.access')"
        href="/admin">
        <v-btn>
            Admin
        </v-btn>
    </Link>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import { useAuthStore } from '@/stores/authStore'

    const auth = useAuthStore()
    
</script>