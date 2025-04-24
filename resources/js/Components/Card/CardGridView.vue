<template>
    <div>
        <!-- Filtry specifické pro grid view -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-text-field
                            v-model="localFilters.search"
                            label="Hledat"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            :loading="isLoading"
                            @update:model-value="debouncedSearch"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="localFilters.type"
                            :items="typeOptions"
                            label="Typ"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('type', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="localFilters.rarity"
                            :items="rarityOptions"
                            label="Vzácnost"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('rarity', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="localFilters.set_id"
                            :items="setOptions"
                            label="Set"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('set_id', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="sortOption"
                            :items="sortOptions"
                            label="Řazení"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            @update:model-value="updateSortOption"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select
                            v-model="localFilters.per_page"
                            :items="perPageOptions"
                            label="Počet na stránku"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            @update:model-value="updateFilter('per_page', $event)"
                        />
                    </v-col>
                </v-row>

                <!-- Aktivní filtry a reset -->
                <v-row v-if="hasActiveFilters" class="mt-2">
                    <v-col cols="12" class="d-flex align-center flex-wrap">
                        <div class="text-caption text-grey me-4">Aktivní filtry:</div>
                        <v-chip
                            v-if="localFilters.search"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('search')"
                        >
                            Hledat: {{ localFilters.search }}
                        </v-chip>
                        <v-chip
                            v-if="localFilters.type"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('type')"
                        >
                            Typ: {{ localFilters.type }}
                        </v-chip>
                        <v-chip
                            v-if="localFilters.rarity"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('rarity')"
                        >
                            Vzácnost: {{ localFilters.rarity }}
                        </v-chip>
                        <v-chip
                            v-if="localFilters.set_id"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('set_id')"
                        >
                            Set: {{ getSetName(localFilters.set_id) }}
                        </v-chip>
                        <v-spacer />
                        <v-btn
                            color="primary"
                            variant="text"
                            prepend-icon="mdi-refresh"
                            @click="resetFilters"
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
                <CardItem :card="card" :is-loading="isLoading" />
            </v-col>
            
            <!-- Stránkování pro mřížku -->
            <v-col cols="12" class="d-flex justify-center mt-4">
                <v-pagination
                    v-model="currentPage"
                    :length="cards.last_page"
                    :total-visible="7"
                    @update:model-value="updatePage"
                    rounded
                    :disabled="isLoading"
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
import { ref, computed, watch, onMounted, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import CardItem from './CardItem.vue';

const page = usePage();
const isLoading = ref(false);

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

// Emitované události
const emit = defineEmits(['update:filters']);

// Lokální filtry
const localFilters = reactive({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    rarity: props.filters?.rarity || '',
    set_id: props.filters?.set_id || '',
    sort_by: props.filters?.sort_by || 'name',
    sort_direction: props.filters?.sort_direction || 'asc',
    per_page: props.filters?.per_page || 30
});

// Stav stránky
const currentPage = ref(props.cards?.current_page || 1);

// Možnosti stránkování
const perPageOptions = [
    { title: '30', value: 30 },
    { title: '60', value: 60 },
    { title: '120', value: 120 }
];

// Filtry
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

// Počítané vlastnosti
const hasCards = computed(() => props.cards && props.cards.data && props.cards.data.length > 0);
const hasActiveFilters = computed(() => localFilters.search || localFilters.type || localFilters.rarity || localFilters.set_id);

// Možnosti řazení
const sortOption = computed({
    get: () => {
        return `${localFilters.sort_by}_${localFilters.sort_direction}`;
    },
    set: (val) => {
        const [sort_by, sort_direction] = val.split('_');
        localFilters.sort_by = sort_by;
        localFilters.sort_direction = sort_direction;
    }
});

const sortOptions = [
    { title: 'Číslo (vzestupně)', value: 'number_asc' },
    { title: 'Číslo (sestupně)', value: 'number_desc' },
    { title: 'Název (A-Z)', value: 'name_asc' },
    { title: 'Název (Z-A)', value: 'name_desc' },
    { title: 'Cena (nejnižší)', value: 'price_asc' },
    { title: 'Cena (nejvyšší)', value: 'price_desc' },
    { title: 'Vzácnost (nejnižší)', value: 'rarity_asc' },
    { title: 'Vzácnost (nejvyšší)', value: 'rarity_desc' },
];

// Sledování změn
watch(() => props.cards?.current_page, (newValue) => {
    if (newValue !== undefined && newValue !== currentPage.value) {
        currentPage.value = newValue;
    }
}, { immediate: true });

// Sledování změn props.filters
watch(() => props.filters, (newFilters) => {
    // Aktualizujeme lokální filtry podle změn v props
    Object.keys(localFilters).forEach(key => {
        if (newFilters[key] !== undefined) {
            localFilters[key] = newFilters[key];
        }
    });
}, { deep: true });

// Debounced funkce pro vyhledávání
const debouncedSearch = debounce((value) => {
    updateFilter('search', value);
}, 500);

// Sledování router událostí pro indikaci načítání
onMounted(() => {
    router.on('start', () => {
        isLoading.value = true;
    });
    
    router.on('finish', () => {
        isLoading.value = false;
    });
    
    // Synchronizace lokalních filtrů s props
    Object.keys(localFilters).forEach(key => {
        if (props.filters[key] !== undefined) {
            localFilters[key] = props.filters[key];
        }
    });
});

// Metody pro filtry
function updateFilter(key, value) {
    localFilters[key] = value;
    applyFilters(true);
}

function updateSortOption(value) {
    const [sort_by, sort_direction] = value.split('_');
    localFilters.sort_by = sort_by;
    localFilters.sort_direction = sort_direction;
    applyFilters(true);
}

function clearFilter(key) {
    localFilters[key] = '';
    applyFilters(true);
}

function resetFilters() {
    localFilters.search = '';
    localFilters.type = '';
    localFilters.rarity = '';
    localFilters.set_id = '';
    localFilters.sort_by = 'name';
    localFilters.sort_direction = 'asc';
    localFilters.per_page = 30;
    applyFilters(true);
}

function applyFilters(resetPage = true) {
    // Vytvoříme nový objekt filtrů
    const newFilters = { ...localFilters };
    
    // Pokud resetujeme stránku, nastavíme ji na 1
    if (resetPage) {
        newFilters.page = 1;
        currentPage.value = 1;
    } else {
        newFilters.page = currentPage.value;
    }
    
    // Informujeme rodičovskou komponentu o změně filtrů
    emit('update:filters', newFilters);
    
    // Odešleme požadavek na server
    router.get('/cards', newFilters, {
        preserveState: true,
        preserveScroll: true,
        only: ['cards']
    });
}

// Metody pro stránkování
function updatePage(newPage) {
    currentPage.value = newPage;
    
    const newFilters = { ...localFilters, page: newPage };
    
    applyFilters(false);
}

// Pomocné metody
function getSetName(setId) {
    if (!setId || !page.props.sets) return '';
    const set = page.props.sets.find(s => s.id === setId);
    return set ? set.name : '';
}
</script>