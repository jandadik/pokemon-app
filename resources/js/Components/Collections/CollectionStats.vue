<template>
    <v-card elevation="2" rounded="xl" class="mb-4">
        <v-card-title class="d-flex align-center pa-4">
            <v-icon class="me-2" color="primary">mdi-chart-box</v-icon>
            {{ $t('collections.stats.title') }}
        </v-card-title>
        <v-card-text class="pa-4 pt-0">
            <v-row>
                <v-col cols="12" sm="3">
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-primary mb-1">
                            {{ formatNumber(stats.total_cards) }}
                        </div>
                        <div class="text-body-2 text-medium-emphasis">
                            {{ $t('collections.stats.total_cards') }}
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" sm="3">
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-success mb-1">
                            {{ formatNumber(stats.unique_cards) }}
                        </div>
                        <div class="text-body-2 text-medium-emphasis">
                            {{ $t('collections.stats.unique_cards') }}
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" sm="3">
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold text-orange mb-1">
                            {{ formatCurrency(stats.total_purchase_value) }}
                        </div>
                        <div class="text-body-2 text-medium-emphasis">
                            {{ $t('collections.stats.purchase_value') }}
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" sm="3">
                    <div class="text-center">
                        <div class="text-h4 font-weight-bold mb-1" :class="getValueDifferenceColor()">
                            {{ formatCurrency(stats.total_market_value) }}
                        </div>
                        <div class="text-body-2 text-medium-emphasis">
                            {{ $t('collections.stats.market_value') }}
                        </div>
                        <div v-if="stats.value_difference !== 0" class="text-caption mt-1" :class="getValueDifferenceColor()">
                            <v-icon size="12" :icon="stats.value_difference > 0 ? 'mdi-trending-up' : 'mdi-trending-down'"></v-icon>
                            {{ formatCurrency(Math.abs(stats.value_difference)) }}
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
        default: () => ({
            total_cards: 0,
            unique_cards: 0,
            total_purchase_value: 0,
            total_market_value: 0,
            value_difference: 0,
        })
    }
});

// Helper funkce pro formátování čísel
const formatNumber = (value) => {
    return new Intl.NumberFormat('cs-CZ').format(value || 0);
};

// Helper funkce pro formátování měny
const formatCurrency = (value) => {
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR'
    }).format(value || 0);
};

// Helper funkce pro barvu rozdílu hodnot
const getValueDifferenceColor = () => {
    if (props.stats.value_difference > 0) {
        return 'text-success';
    } else if (props.stats.value_difference < 0) {
        return 'text-error';
    } else {
        return 'text-warning';
    }
};
</script>

<style scoped>
/* Žádné specifické styly potřeba */
</style> 