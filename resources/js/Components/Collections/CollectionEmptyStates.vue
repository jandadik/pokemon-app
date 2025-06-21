<template>
    <!-- Prázdná kolekce (žádné položky vůbec) -->
    <v-card v-if="isCollectionEmpty" class="text-center pa-12" elevation="2" rounded="xl">
        <v-icon size="120" color="grey-lighten-2" class="mb-4">mdi-cards-outline</v-icon>
        <h2 class="text-h4 mb-4 text-medium-emphasis">{{ $t('collections.items.empty_title') }}</h2>
        <p class="text-body-1 mb-6 text-medium-emphasis">
            {{ $t('collections.items.empty_text') }}
        </p>
        <v-btn
            v-if="can && can.update"
            color="primary"
            size="large"
            prepend-icon="mdi-plus"
            rounded="xl"
            @click="$emit('add-item')"
        >
            {{ $t('collections.items.add_first_card') }}
        </v-btn>
    </v-card>

    <!-- Žádné výsledky filtrování -->
    <v-card v-else-if="hasNoFilterResults" class="text-center pa-12" elevation="2" rounded="xl">
        <v-icon size="120" color="grey-lighten-2" class="mb-4">mdi-filter-remove-outline</v-icon>
        <h2 class="text-h4 mb-4 text-medium-emphasis">{{ $t('collections.filters.no_results_title') }}</h2>
        <p class="text-body-1 mb-6 text-medium-emphasis">
            {{ $t('collections.filters.no_results_text') }}
        </p>
        <div class="d-flex gap-3 justify-center">
            <v-btn
                color="primary"
                variant="outlined"
                prepend-icon="mdi-filter-off"
                rounded="xl"
                @click="$emit('reset-filters')"
            >
                {{ $t('collections.filters.reset') }}
            </v-btn>
            <v-btn
                v-if="can && can.update"
                color="primary"
                prepend-icon="mdi-plus"
                rounded="xl"
                @click="$emit('add-item')"
            >
                {{ $t('collections.items.add_new_item') }}
            </v-btn>
        </div>
    </v-card>
</template>

<script setup>
import { getCurrentInstance } from 'vue';

const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

defineProps({
    isCollectionEmpty: Boolean,
    hasNoFilterResults: Boolean,
    can: Object,
});

defineEmits([
    'add-item',
    'reset-filters'
]);
</script> 