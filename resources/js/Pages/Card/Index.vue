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
                                    :value="viewMode"
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
            v-if="viewMode === 'grid'"
            :cards="cards"
            :filters="filters"
            @update:filters="updateComponentFilters"
        />

        <!-- Seznam pohled -->
        <card-list-view
            v-else
            :cards="cards"
            :filters="filters"
            @update:filters="updateComponentFilters"
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
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';

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

// Stav aplikace
const viewMode = ref('grid');
const isLoading = ref(false);

// Nastavení výchozích filtrů
const filters = ref({
    search: props.filters.search || '',
    type: props.filters.type || '',
    rarity: props.filters.rarity || '',
    set_id: props.filters.set_id || '',
    sort_by: props.filters.sort_by || 'name',
    sort_direction: props.filters.sort_direction || 'asc',
    per_page: props.filters.per_page || 30,
    page: props.filters.page || 1,
    view: props.filters.view || 'grid'
});

// Počet karet celkem
const totalCards = computed(() => {
    return props.cards?.total || 0;
});

// Inicializace
watch(() => filters.value.view, (newView) => {
    if (newView && viewMode.value !== newView) {
        // Nastavíme pohled bez volání switchView
        viewMode.value = newView;
    }
}, { immediate: true });

// Metoda pro aktualizaci filtrů z child komponent
function updateComponentFilters(newFilters) {
    // Aktualizace lokálních filtrů podle komponent
    Object.keys(newFilters).forEach(key => {
        filters.value[key] = newFilters[key];
    });
}

// Funkce pro přepnutí pohledu
function switchView(newView) {
    // Nastavím nový pohled
    viewMode.value = newView;
    
    // Uložíme preference do localStorage
    localStorage.setItem('preferredCardView', newView);
    
    // Resetujeme všechny filtry
    filters.value = {
        view: newView,
        search: '',
        type: '',
        rarity: '',
        set_id: '',
        page: 1,
        per_page: 30,
        sort_by: 'name',
        sort_direction: 'asc'
    };
    
    // Nejprve vyčistíme URL od všech parametrů
    window.history.pushState({}, '', '/cards');
    
    // Pak odešleme request pouze s parametrem view
    router.visit('/cards?view=' + newView, {
        method: 'get',
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['cards']
    });
}

// Funkce pro přesun zpět nahoru
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Sledování navigačních událostí
router.on('start', () => {
    isLoading.value = true;
});

router.on('finish', () => {
    isLoading.value = false;
});
</script>

<style scoped>
.scroll-to-top-btn {
    position: fixed;
    bottom: 20px;
    right: 20px; 
    z-index: 100;
}
</style> 