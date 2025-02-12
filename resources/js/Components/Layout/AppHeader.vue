<template>
    <v-app-bar :elevation="2">
        <!-- Levá část -->
        <template v-slot:prepend>
            <v-app-bar-nav-icon 
                variant="text" 
                @click="$emit('update:drawer', !drawer)"
            />
        </template>

        <!-- Střední část -->
        <Link :href="route('dashboard')">
            <ApplicationLogo class="h-9 w-auto fill-current" />
        </Link>
        <v-app-bar-title>Poke App</v-app-bar-title>

        <!-- Pravá část -->
        <template v-slot:append>
            <AppHeaderActions @toggle-user="$emit('update:user', !user)" />
        </template>

        <!-- Spodní část s taby -->
        <template v-slot:extension>
            <v-container class="pa-0">
                <div class="d-flex justify-center">
                    <AppTabs />
                </div>
            </v-container>
        </template>
    </v-app-bar>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import ApplicationLogo from '@/Components/ApplicationLogo.vue'
    import AppHeaderActions from '@/Components/Layout/AppHeaderActions.vue'
    import AppTabs from '@/Components/Layout/AppTabs.vue'

    defineProps({
        drawer: Boolean,
        user: Boolean
    })

    defineEmits(['update:drawer', 'update:user', 'toggle-user'])
</script>