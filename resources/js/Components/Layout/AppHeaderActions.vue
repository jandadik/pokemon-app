<template>
    <!-- Tlačítka viditelná na všech zařízeních -->
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-heart"
        class="d-none d-md-flex"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-heart</v-icon>
        <span v-else>{{ $trans('ui.header.favorites') }}</span>
    </v-btn>
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-magnify"
        class="d-none d-md-flex"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-magnify</v-icon>
        <span v-else>{{ $trans('ui.header.search') }}</span>
    </v-btn>
    
    <!-- Přepínač jazyka - Viditelný pouze na větších zařízeních -->
    <LanguageSwitcher class="d-none d-md-flex" />
    
    <!-- Přepínač tématu - Viditelný pouze na větších zařízeních -->
    <ThemeSwitcher class="d-none d-md-flex" />
    
    <!-- Menu s třemi tečkami pro mobilní zařízení -->
    <v-menu v-if="$vuetify.display.smAndDown">
        <template v-slot:activator="{ props }">
            <v-btn icon v-bind="props">
                <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
        </template>
        <v-list>
            <v-list-item>
                <v-list-item-title>
                    <v-icon start>mdi-heart</v-icon>
                    {{ $trans('ui.header.favorites') }}
                </v-list-item-title>
            </v-list-item>
            <v-list-item>
                <v-list-item-title>
                    <v-icon start>mdi-magnify</v-icon>
                    {{ $trans('ui.header.search') }}
                </v-list-item-title>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <v-list-item-title>
                    <v-icon start>mdi-translate</v-icon>
                    {{ $trans('ui.header.language') }}
                </v-list-item-title>
                <template v-slot:append>
                    <LanguageSwitcher />
                </template>
            </v-list-item>
            <v-list-item>
                <v-list-item-title>
                    <v-icon start>mdi-theme-light-dark</v-icon>
                    {{ $trans('ui.header.theme') }}
                </v-list-item-title>
                <template v-slot:append>
                    <ThemeSwitcher />
                </template>
            </v-list-item>
        </v-list>
    </v-menu>
    
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
                <Link :href="route('user.profile')">
                    <v-list-item-title>
                        <v-icon start>mdi-account</v-icon>
                        {{ $trans('ui.header.profile') }}
                    </v-list-item-title>
                </Link>
            </v-list-item>
            <v-list-item>
                <Link :href="route('user.profile', { tab: 'settings' })">
                    <v-list-item-title>
                        <v-icon start>mdi-cog</v-icon>
                        {{ $trans('ui.header.settings') }}
                    </v-list-item-title>
                </Link>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item>
                <Link :href="route('auth.logout')" method="delete" as="button">
                    <v-list-item-title>
                        <v-icon start>mdi-logout</v-icon>
                        {{ $trans('ui.header.logout') }}
                    </v-list-item-title>
                </Link>
            </v-list-item>
        </v-list>
    </v-menu>
    
    <!-- Přihlášení a registrace pro nepřihlášeného uživatele -->
    <template v-else>
        <Link :href="route('auth.login')">
            <v-btn 
                :icon="$vuetify.display.mobile"
                :variant="$vuetify.display.mobile ? 'plain' : 'text'"
            >
                <v-icon v-if="$vuetify.display.mobile">mdi-login</v-icon>
                <span v-else>{{ $trans('ui.header.login') }}</span>
            </v-btn>
        </Link>
    </template>
</template>

<script setup>
    import { useDisplay } from 'vuetify'
    import { Link } from '@inertiajs/vue3'
    import { useAuthStore } from '@/stores/authStore'
    import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
    import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
    
    const auth = useAuthStore()
</script>