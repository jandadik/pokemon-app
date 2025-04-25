<template>
    <div>
        <!-- Filtry specifické pro grid view -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-text-field
                            v-model="cardStore.search"
                            label="Hledat"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            :loading="cardStore.isLoading"
                            @update:model-value="cardStore.setSearch"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="cardStore.type"
                            :items="typeOptions"
                            label="Typ"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="cardStore.setType"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="cardStore.rarity"
                            :items="rarityOptions"
                            label="Vzácnost"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="cardStore.setRarity"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="cardStore.set_id"
                            :items="setOptions"
                            label="Set"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="cardStore.setSetId"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="cardStore.sortOption"
                            :items="sortOptions"
                            label="Řazení"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            @update:model-value="cardStore.setSortOption"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="cardStore.per_page"
                            :items="perPageOptions"
                            label="Počet na stránku"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            @update:model-value="cardStore.setPerPage"
                        />
                    </v-col>
                </v-row>

                <!-- Aktivní filtry a reset -->
                <v-row v-if="cardStore.hasActiveFilters" class="mt-2">
                    <v-col cols="12" class="d-flex align-center flex-wrap">
                        <div class="text-caption text-grey me-4">Aktivní filtry:</div>
                        <v-chip
                            v-if="cardStore.search"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('search')"
                        >
                            Hledat: {{ cardStore.search }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.type"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('type')"
                        >
                            Typ: {{ cardStore.type }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.rarity"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('rarity')"
                        >
                            Vzácnost: {{ cardStore.rarity }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.set_id"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('set_id')"
                        >
                            Set: {{ getSetName(cardStore.set_id) }}
                        </v-chip>
                        <v-spacer />
                        <v-btn
                            color="primary"
                            variant="text"
                            prepend-icon="mdi-refresh"
                            @click="cardStore.resetFilters"
                        >
                            Resetovat filtry
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Zobrazení karet - mřížka -->
        <v-row v-if="hasCards">
            <v-col 
                v-for="card in cards.data" 
                :key="card.id"
                cols="6"
                sm="4"
                md="3"
                lg="2"
                xl="2"
                class="mb-4"
            >
                <CardItem :card="card" :is-loading="cardStore.isLoading" />
            </v-col>
            
            <!-- Stránkování pro mřížku -->
            <v-col cols="12" class="d-flex justify-center mt-4">
                <v-pagination
                    v-model="cardStore.currentPage"
                    :length="cards.last_page"
                    :total-visible="7"
                    @update:model-value="cardStore.setPage"
                    rounded
                    :disabled="cardStore.isLoading"
                ></v-pagination>
            </v-col>
        </v-row>

        <!-- Prázdný stav -->
        <v-row v-else justify="center" align="center" style="min-height: 400px">
            <v-col cols="12" md="8" lg="6">
                <v-alert
                    type="info"
                    variant="tonal"
                    class="text-center"
                >
                    {{ $t('catalog.cards.no_cards') || 'Nebyly nalezeny žádné karty' }}
                </v-alert>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import CardItem from './CardItem.vue';
import { useCardStore } from '@/stores/cardStore';

const page = usePage();
const cardStore = useCardStore();

const props = defineProps({
    cards: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    }
});

// Možnosti stránkování
const perPageOptions = [
    { title: '30', value: 30 },
    { title: '60', value: 60 },
    { title: '120', value: 120 }
];

// Typy pro filtrování
const typeOptions = [
    { title: 'Všechny typy', value: '' },
    { title: 'Pokémon', value: 'Pokémon' },
    { title: 'Trenér', value: 'Trainer' },
    { title: 'Energie', value: 'Energy' },
];

// Vzácnosti pro filtrování
const rarityOptions = [
    { title: 'Všechny vzácnosti', value: '' },
    { title: 'Common', value: 'Common' },
    { title: 'Uncommon', value: 'Uncommon' },
    { title: 'Rare', value: 'Rare' },
    { title: 'Rare Holo', value: 'Rare Holo' },
    { title: 'Rare Ultra', value: 'Rare Ultra' },
    { title: 'Rare Secret', value: 'Rare Secret' },
    { title: 'Amazing Rare', value: 'Amazing Rare' },
    { title: 'Ultra Rare', value: 'Ultra Rare' },
    { title: 'Secret Rare', value: 'Secret Rare' },
    { title: 'Promo', value: 'Promo' }
];

// Sety pro filtrování
const setOptions = computed(() => {
    const options = [{ title: 'Všechny sety', value: '' }];
    
    if (page.props.sets && page.props.sets.length > 0) {
        // Seřadíme sety podle názvu
        const sortedSets = [...page.props.sets].sort((a, b) => a.name.localeCompare(b.name));
        
        sortedSets.forEach(set => {
            options.push({ title: set.name, value: set.id });
        });
    }
    
    return options;
});

// Možnosti řazení - OPRAVENO KLÍČE CENY
const sortOptions = [
    { title: 'Číslo (vzestupně)', value: 'number_asc' },
    { title: 'Číslo (sestupně)', value: 'number_desc' },
    { title: 'Název (A-Z)', value: 'name_asc' },
    { title: 'Název (Z-A)', value: 'name_desc' },
    // Použít správné klíče z backendu
    { title: 'Cena (nejnižší)', value: 'cm_avg30_asc' }, 
    { title: 'Cena (nejvyšší)', value: 'cm_avg30_desc' },
    // Volitelně přidat i trend cenu:
    // { title: 'Cena Trend (nejnižší)', value: 'price_cm_trend_asc' }, 
    { title: 'Vzácnost (nejnižší)', value: 'rarity_asc' },
    { title: 'Vzácnost (nejvyšší)', value: 'rarity_desc' },
];

// Počítané vlastnosti
const hasCards = computed(() => props.cards && props.cards.data && props.cards.data.length > 0);

// Inicializace stavu
onMounted(() => {
    // Inicializujeme store z props
    cardStore.initializeFromProps(props);
    // Načteme preferované zobrazení z localStorage
    cardStore.initializeFromLocalStorage();
});

// Pomocné metody
function clearFilter(key) {
    if (key === 'search') cardStore.setSearch('');
    if (key === 'type') cardStore.setType('');
    if (key === 'rarity') cardStore.setRarity('');
    if (key === 'set_id') cardStore.setSetId('');
}

function getSetName(setId) {
    if (!setId || !page.props.sets) return '';
    const set = page.props.sets.find(s => s.id === setId);
    return set ? set.name : '';
}
</script>