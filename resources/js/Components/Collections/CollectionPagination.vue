<template>
    <!-- Stránkování -->
    <v-row class="mt-4" align="center" justify="space-between">
        <v-col cols="auto">
            <v-pagination
                :model-value="currentPage"
                :length="totalPages"
                @update:model-value="$emit('page-change', $event)"
                rounded="sm"
                show-first-last-page
            />
        </v-col>
        <v-col cols="auto">
            <v-select
                :model-value="perPage"
                :items="perPageOptions"
                :label="$t('collections.filters.per_page')"
                variant="outlined"
                density="compact"
                hide-details
                style="max-width: 120px"
                @update:model-value="$emit('per-page-change', $event)"
            />
        </v-col>
    </v-row>
</template>

<script setup>
import { getCurrentInstance } from 'vue';

const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

defineProps({
    currentPage: Number,
    totalPages: Number,
    perPage: Number,
    perPageOptions: Array,
});

defineEmits([
    'page-change',
    'per-page-change'
]);
</script> 