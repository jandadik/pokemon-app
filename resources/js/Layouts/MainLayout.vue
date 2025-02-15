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
                @update:user="user = $event"
            />
            <!-- Hlavní obsah -->
            <v-main>
                <slot></slot>
            </v-main>
        </v-app>
    </v-responsive>
</template>

<script setup>
    import {ref, onMounted} from 'vue'
    import AppHeader from '@/Components/Layout/AppHeader.vue'
    import AppNavDrawer from '@/Components/Layout/AppNavDrawer.vue'
    

    const drawer = ref(false)
    const user = ref(false)

</script>
