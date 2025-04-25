<template>
    <v-container>
    <Head :title="$t('catalog.cards.title') || 'Katalog karet'" />

        <!-- Hlavička stránky -->
        <v-card class="mb-4 page-header" id="top">
            <v-card-text>
                <v-row align="center">
                    <v-col cols="12" md="2" class="d-flex justify-center align-center">
                        <div class="page-logo-container">
                            <v-icon
                                size="64"
                                color="primary"
                                icon="mdi-cards"
                                class="mx-auto"
                            />
                        </div>
                    </v-col>
                    <v-col cols="12" md="10">
                        <div class="d-flex flex-wrap align-center justify-space-between">
                            <h1 class="text-h4">{{ $t('catalog.cards.all_cards') || 'Všechny karty' }}</h1>
                            
                            <div class="d-flex align-center">
                                <v-chip
                                    class="me-2"
                                    color="primary"
                                    variant="outlined"
                                >
                                    {{ totalCards }} {{ $t('catalog.sets.cards_count') || 'karet' }}
                                </v-chip>
                                
                                <v-btn-toggle
                                    :model-value="cardStore.view_mode"
                                    color="primary"
                                    density="comfortable"
                                >
                                    <v-btn value="grid" icon="mdi-view-grid" @click="switchView('grid')"></v-btn>
                                    <v-btn value="list" icon="mdi-view-list" @click="switchView('list')"></v-btn>
                                </v-btn-toggle>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Grid pohled -->
        <card-grid-view
            v-if="cardStore.view_mode === 'grid'"
            :cards="cards"
            :filters="cardStore.filters"
        />

        <!-- Seznam pohled -->
        <card-list-view
            v-else
            :cards="cards"
            :filters="cardStore.filters"
        />
        
        <!-- Plovoucí tlačítko pro přesun zpět nahoru -->
        <v-btn
            fab
            color="primary"
            icon="mdi-arrow-up"
            @click="scrollToTop"
            class="scroll-to-top-btn"
        ></v-btn>
    </v-container>
</template>

<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useCardStore } from '@/stores/cardStore';

import CardGridView from '@/Components/Card/CardGridView.vue';
import CardListView from '@/Components/Card/CardListView.vue';

// Props z Inertia
const props = defineProps({
    cards: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Inicializace Pinia store
const cardStore = useCardStore();

// Počet karet celkem
const totalCards = computed(() => {
    return props.cards?.total || 0;
});

// Funkce pro přepnutí pohledu - OPRAVENO
function switchView(newView) {
    // Zavolat správnou akci ve store
    cardStore.setViewMode(newView); // Správný název akce
}

// Funkce pro přesun zpět nahoru
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>

<style scoped>
.scroll-to-top-btn {
    position: fixed;
    bottom: 20px;
    right: 20px; 
    z-index: 100;
}
</style> 