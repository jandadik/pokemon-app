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
                            @update:model-value="updateFilter('search', $event)"
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
                <v-card
                    :to="`/cards/${card.id}`"
                    class="h-100 card-item"
                    hover
                >
                    <div class="card-img-container">
                        <img 
                            :src="getCardImageUrl(card)" 
                            :alt="card.name"
                            class="card-img"
                            @error="handleImageError($event)"
                        >
                        <div class="card-number">{{ formatCardNumber(card.number) }}</div>
                        <div v-if="card.set" class="card-set-name">{{ card.set.name }}</div>
                    </div>

                    <v-card-text class="pa-0 pt-3 d-flex justify-space-between align-center px-3 pb-3">
                        <div :class="getRarityClass(card.rarity)" class="card-rarity text-caption">
                            {{ card.rarity }}
                        </div>
                        <div v-if="card.prices_cm && card.prices_cm.avg30 !== null" class="text-caption price-tag">
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <span v-bind="props">
                                        {{ formatNumberPrice(card.prices_cm.avg30) }}
                                        <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                                    </span>
                                </template>
                                <div>
                                    <div>Aktualizace dne: {{ formatUpdateDate(card.prices_cm.updated_at) }}</div>
                                    <div class="d-flex flex-column mt-1">
                                        <div v-if="card.prices_cm.avg30 !== null" class="text-caption">
                                            Cena (normální): {{ formatPrice(card.prices_cm.avg30) }}
                                        </div>
                                        <div v-if="card.prices_cm.reverse_holo_avg30 !== null" class="text-caption">
                                            Cena (reverse holo): {{ formatPrice(card.prices_cm.reverse_holo_avg30) }}
                                        </div>
                                        <div class="text-caption mt-1">ID: {{ card.prices_cm.card_id }}</div>
                                    </div>
                                </div>
                            </v-tooltip>
                        </div>
                        <div v-else class="text-caption text-medium-emphasis">
                            -
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            
            <!-- Stránkování pro mřížku -->
            <v-col cols="12" class="d-flex justify-center mt-4">
                <v-pagination
                    v-model="currentPage"
                    :length="cards.last_page"
                    :total-visible="7"
                    @update:model-value="updatePage"
                    rounded
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
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';

const page = usePage();

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

// Inicializace při načtení komponenty
onMounted(() => {
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

function getCardImageUrl(card) {
    // Pokud není karta definována, vrátíme placeholder
    if (!card) {
        return '/images/placeholder.jpg';
    }
    
    // Priorita 1: Lokální soubor z databáze (img_file_small)
    if (card.img_file_small) {
        // V databázi je relativní cesta "card_images\cel25\1.png" bez '/images/'
        // Převést obrácená lomítka na normální a přidat počáteční /images/
        const path = '/images/' + card.img_file_small.replace(/\\/g, '/');
        return path;
    }
    
    // Priorita 2: URL z API/externího zdroje (img_small)
    if (card.img_small) {
        return card.img_small;
    }
    
    // Fallback: Generovaná cesta podle čísla karty a setu
    if (card.set_id && card.number) {
        const localPath = `/images/card_images/${card.set_id}/${card.number}.png`;
        return localPath;
    }
    
    // Fallback: Placeholder
    return '/images/placeholder.jpg';
}

function getRarityClass(rarity) {
    if (!rarity) return 'text-grey';
    
    // Převedeme rarity na lowercase bez mezer pro jednodušší mapování
    const rarityKey = rarity.toLowerCase().replace(/\s+/g, '-');
    
    const classes = {
        'common': 'text-common',
        'uncommon': 'text-uncommon',
        'rare': 'text-rare',
        'rare-holo': 'text-rare-holo',
        'rare-ultra': 'text-rare-ultra',
        'rare-secret': 'text-rare-secret',
        'amazing-rare': 'text-rare-holo',
        'ultra-rare': 'text-rare-ultra',
        'secret-rare': 'text-rare-secret',
        'promo': 'text-uncommon',
    };
    
    return classes[rarityKey] || 'text-grey';
}

function formatCardNumber(number) {
    if (!number) return '000';
    
    // Extrahujeme číselnou část
    const numericPart = String(number).replace(/\D/g, '');
    
    // Doplníme nuly zleva
    return numericPart.padStart(3, '0');
}

function formatNumberPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    
    // Formátování ceny v EUR bez ikony (ikona je přidána v template)
    return new Intl.NumberFormat('cs-CZ', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}

function formatPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    
    // Formátování ceny pro tooltip
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}

function formatUpdateDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return format(date, 'd. MMMM yyyy', { locale: cs });
}

// Přidám funkci pro ošetření chyby načítání obrázku
function handleImageError(event) {
    console.log('Chyba načítání obrázku', event.target.src);
    event.target.src = '/images/placeholder.jpg';
}
</script>

<style scoped>
.card-item {
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
    position: relative;
}

.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
}

.card-img-container {
    position: relative;
    overflow: hidden;
    background-color: #f5f5f5;
    padding-top: 140%; /* Poměr stran 1:1.4 pro karty Pokémon */
}

.card-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.card-item:hover .card-img {
    transform: scale(1.03);
}

.card-number {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
    z-index: 1;
    line-height: 1.2;
}

.card-set-name {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 500;
    z-index: 1;
    line-height: 1.2;
    max-width: 65%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-rarity {
    max-width: 70%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.price-tag {
    font-weight: 500;
    color: #388e3c;
    white-space: nowrap;
}

/* Styly pro různé vzácnosti karet */
.text-common {
    color: #666;
}

.text-uncommon {
    color: #1976d2;
}

.text-rare {
    color: #9c27b0;
    font-weight: 500;
}

.text-rare-holo, .text-rare-ultra, .text-rare-secret {
    background: linear-gradient(45deg, #ff9800, #f44336, #9c27b0, #3f51b5);
    background-size: 200% auto;
    color: transparent;
    -webkit-background-clip: text;
    background-clip: text;
    animation: gradient 3s ease infinite;
    font-weight: bold;
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}
</style> 