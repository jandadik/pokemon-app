<template>
    <!-- Hlavička - řádek 2: hledání, filtry, grid/list -->
    <div class="mb-3">
        <SkeletonLoader v-if="isLoadingData" type="filters" />
        <CollectionFilters
            v-else
            :filterOptions="filterOptions"
        />
        <v-row align="center" dense>
            <v-spacer></v-spacer>
            <v-col cols="auto">
                <v-btn
                    v-if="hasActiveFilters"
                    variant="outlined"
                    size="small"
                    prepend-icon="mdi-filter-off"
                    @click="$emit('reset-filters')"
                    class="me-2"
                >
                    {{ $t('collections.filters.reset') }}
                </v-btn>
            </v-col>
            <v-col cols="auto">
                <v-btn
                    v-if="!bulkMode"
                    variant="outlined"
                    size="small"
                    prepend-icon="mdi-checkbox-multiple-marked"
                    @click="$emit('toggle-bulk-mode')"
                    class="me-2"
                >
                    Vybrat
                </v-btn>
                <v-btn
                    v-else
                    variant="tonal"
                    size="small"
                    prepend-icon="mdi-close"
                    @click="$emit('toggle-bulk-mode')"
                    class="me-2"
                >
                    Zrušit
                </v-btn>
            </v-col>
            <v-col cols="auto">
                <v-btn-toggle :model-value="viewMode" @update:model-value="$emit('update:view-mode', $event)" mandatory rounded="sm" size="small">
                    <v-btn value="grid" icon="mdi-view-grid" size="small">
                        <v-icon>mdi-view-grid</v-icon>
                        <v-tooltip activator="parent" location="bottom">Mřížka</v-tooltip>
                    </v-btn>
                    <v-btn value="list" icon="mdi-view-list" size="small">
                        <v-icon>mdi-view-list</v-icon>
                        <v-tooltip activator="parent" location="bottom">Seznam</v-tooltip>
                    </v-btn>
                </v-btn-toggle>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue';
import CollectionFilters from './CollectionFilters.vue';
import SkeletonLoader from './SkeletonLoader.vue';

const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

defineProps({
    filterOptions: Object,
    hasActiveFilters: Boolean,
    bulkMode: Boolean,
    viewMode: String,
    isLoadingData: Boolean,
});

defineEmits([
    'reset-filters',
    'toggle-bulk-mode',
    'update:view-mode'
]);
</script> 