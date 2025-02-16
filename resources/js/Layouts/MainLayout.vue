<template>
    <v-responsive>
        <v-app>
            <!-- Levý drawer s menu -->
            <AppNavDrawer
                :model-value="drawer"
                @update:model-value="drawer = $event"
            />
            <!-- Hlavní AppBar s taby -->
            <AppHeader 
                :drawer="drawer"
                @update:drawer="drawer = $event"
            />
            <!-- Hlavní obsah -->
            <v-main>
                <slot></slot>
            </v-main>
        </v-app>
    </v-responsive>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppHeader from '@/Components/Layout/AppHeader.vue'
import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
import { usePage } from '@inertiajs/vue3'

const drawer = ref(false)
const page = usePage()

const flashSuccess = computed(
    () => page.props.flash.success,
)
</script>
