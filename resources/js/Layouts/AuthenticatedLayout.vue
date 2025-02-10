<template>
    <v-responsive class="border rounded">
        <v-app>
            <!-- Hlavní AppBar s taby -->
            <AppHeader 
                :drawer="drawer"
                :user="user"
                @update:drawer="drawer = $event"
                @update:user="user = $event"
            />

            <!-- Levý drawer s menu -->
            <AppNavDrawer
                :model-value="drawer"
                @update:model-value="drawer = $event"
            />

            <!-- Pravý drawer s uživatelským menu -->
            <UserNavDrawer
                :model-value="user"
                :auth-user="$page.props.auth.user"
                @update:model-value="user = $event"
            />

            <!-- Hlavní obsah -->
            <v-main>
                <slot />
            </v-main>
        </v-app>
    </v-responsive>
</template>

<script setup>
    import { ref } from 'vue'
    import AppHeader from '@/Components/Layout/AppHeader.vue'
    import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
    import UserNavDrawer from '@/Components/Layout/UserNavDrawer.vue'

    const drawer = ref(false);
    const user = ref(false);
</script>