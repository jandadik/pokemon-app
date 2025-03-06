<template>
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-heart"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-heart</v-icon>
        <span v-else>Oblíbené</span>
    </v-btn>
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-magnify"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-magnify</v-icon>
        <span v-else>Hledat</span>
    </v-btn>
    
    <!-- Přepínač tématu -->
    <ThemeSwitcher />
    
    <!-- Další akce pro přihlášeného uživatele -->
    <v-menu v-if="auth.isLoggedIn">
        <template v-slot:activator="{ props }">
            <v-btn icon v-bind="props">
                <v-avatar size="32">
                    <v-img src="https://randomuser.me/api/portraits/men/85.jpg" alt="user" />
                </v-avatar>
            </v-btn>
        </template>
        <v-list>
            <v-list-item>
                <Link :href="route('profile')">
                    <v-list-item-title>
                        <v-icon start>mdi-account</v-icon>
                        Profil
                    </v-list-item-title>
                </Link>
            </v-list-item>
            <v-list-item>
                <Link :href="route('profile', { tab: 'settings' })">
                    <v-list-item-title>
                        <v-icon start>mdi-cog</v-icon>
                        Nastavení
                    </v-list-item-title>
                </Link>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <Link :href="route('logout')" method="delete" as="button">
                    <v-list-item-title>
                        <v-icon start>mdi-logout</v-icon>
                        Odhlásit
                    </v-list-item-title>
                </Link>
            </v-list-item>
        </v-list>
    </v-menu>
    
    <!-- Přihlášení a registrace pro nepřihlášeného uživatele -->
    <template v-else>
        <Link :href="route('login')">
            <v-btn 
                :icon="$vuetify.display.mobile"
                :variant="$vuetify.display.mobile ? 'plain' : 'text'"
            >
                <v-icon v-if="$vuetify.display.mobile">mdi-login</v-icon>
                <span v-else>Přihlásit</span>
            </v-btn>
        </Link>
    </template>
</template>

<script setup>
    import { useDisplay } from 'vuetify'
    import { Link } from '@inertiajs/vue3'
    import { useAuthStore } from '@/stores/authStore'
    import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
    
    const auth = useAuthStore()
</script>