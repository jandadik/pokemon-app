<template>
    <v-app-bar :elevation="2">
        <div class="header-container d-flex align-center">
            <!-- Levá část -->
            <v-app-bar-nav-icon 
                variant="text" 
                @click="$emit('update:drawer', !drawer)"
                class="flex-shrink-0"
            />

            <!-- Střední část -->
            <Link href="/" class="ml-3 mr-2">
                <ApplicationLogo class="" />
            </Link>
            <v-app-bar-title>{{ $trans('app.app_name') }}</v-app-bar-title>

            <v-spacer></v-spacer>

            <!-- Pravá část -->
            <AppHeaderActions />
        </div>

        <!-- Spodní část s taby -->
        <template v-slot:extension v-if="showTabs">
            <div class="header-container">
                <div class="d-flex justify-center">
                    <AppTabs />
                </div>
            </div>
        </template>
    </v-app-bar>
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

