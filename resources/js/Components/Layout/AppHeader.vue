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
            <ApplicationLogo class="" />
        </Link>
        <v-app-bar-title>Poke App</v-app-bar-title>

        <!-- Pravá část -->
        <template v-slot:append>
            <AppHeaderActions @toggle-user="$emit('update:user', !user)" />
        </template>

        <!-- Spodní část s taby -->
        <template v-slot:extension v-if="showTabs">
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
    import ApplicationLogo from '@/components/ApplicationLogo.vue'
    import AppHeaderActions from '@/components/layout/AppHeaderActions.vue'
    import AppTabs from '@/components/layout/AppTabs.vue'

    defineProps({
        drawer: Boolean,
        user: Boolean,
        showTabs: {
            type: Boolean,
            default: true
        }
    })

    defineEmits(['update:drawer', 'update:user', 'toggle-user'])
</script>