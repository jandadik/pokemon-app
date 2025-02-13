<template>
    <Head :title="`Položky číselníku - ${category.name}`" />
    <AuthenticatedLayout>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-btn
                        color="primary"
                        class="mb-4"
                        :href="route('admin.index')"
                        prepend-icon="mdi-arrow-left"
                    >
                        Zpět na číselníky
                    </v-btn>
                    
                    <v-card>
                        <RegisterItems
                            :category="category"
                            :registers="registers"
                        />
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import RegisterItems from '@/components/admin/RegisterItems.vue';

defineProps({
    category: {
        type: Object,
        required: true
    },
    registers: {
        type: Array,
        required: true
    }
});
</script> 