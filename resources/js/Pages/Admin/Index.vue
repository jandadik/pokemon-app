<template>
    <Head title="Administrace" />
    <AuthenticatedLayout>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">Administrace</h1>
                </v-col>
            </v-row>
            
            <v-row>
                <v-col cols="12" md="3">
                    <!-- Menu administrace -->
                    <AdminMenu />
                </v-col>
                
                <v-col cols="12" md="9">
                    <!-- Obsah administrace -->
                    <v-card v-if="categories">
                        <RegisterCategories :categories="categories" />
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import AdminMenu from '@/aomponents/admin/AdminMenu.vue';
import RegisterCategories from '@/aomponents/admin/RegisterCategories.vue';

defineProps({
    categories: {
        type: Array,
        default: () => []
    }
});
</script> 