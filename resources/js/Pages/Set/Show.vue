<template>
    <v-container>
        <!-- Hlavička setu -->
        <v-card class="mb-4 set-header">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="3" class="d-flex justify-center align-center">
                        <div class="set-logo-container">
                            <v-img
                                :src="set.logo_url"
                                :alt="set.name"
                                height="150"
                                max-width="85%"
                                contain
                                position="center center"
                                class="mx-auto px-6 py-3 rounded-sm"
                            >
                                <template v-slot:placeholder>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-progress-circular
                                            indeterminate
                                            color="grey-lighten-5"
                                        />
                                    </v-row>
                                </template>
                                <template v-slot:error>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-icon
                                            size="large"
                                            color="grey-lighten-1"
                                        >
                                            mdi-cards
                                        </v-icon>
                                    </v-row>
                                </template>
                            </v-img>
                        </div>
                    </v-col>
                    <v-col cols="12" md="9">
                        <div class="d-flex align-center mb-2">
                            <div class="symbol-wrapper me-2 flex-shrink-0">
                                <img 
                                    v-if="set.ptcgo_code && checkImageExists(`/images/symbols/${set.ptcgo_code.toLowerCase()}.png`)"
                                    :src="`/images/symbols/${set.ptcgo_code.toLowerCase()}.png`" 
                                    :alt="set.name"
                                    class="symbol-img" 
                                    width="32"
                                    height="32"
                                />
                                <img 
                                    v-else-if="set.ptcgo_code && checkImageExists(`/images/symbols/${set.ptcgo_code.toLowerCase()}.svg`)"
                                    :src="`/images/symbols/${set.ptcgo_code.toLowerCase()}.svg`" 
                                    :alt="set.name"
                                    class="symbol-img" 
                                    width="32"
                                    height="32"
                                />
                                <img 
                                    v-else-if="set.symbol_url"
                                    :src="set.symbol_url" 
                                    :alt="set.name"
                                    class="symbol-img" 
                                    width="32"
                                    height="32"
                                />
                                <v-icon 
                                    v-else
                                    icon="mdi-cards"
                                    size="32"
                                    class="symbol-img text-primary" 
                                />
                            </div>
                            <h1 class="text-h4">{{ set.name }}</h1>
                        </div>
                        
                        <div class="mb-2">
                            <v-chip
                                class="me-2 mb-2"
                                color="primary"
                                variant="outlined"
                            >
                                {{ set.series }}
                            </v-chip>
                            <v-chip
                                class="me-2 mb-2"
                                color="primary"
                                variant="outlined"
                            >
                                {{ formatDate(set.release_date) }}
                            </v-chip>
                            <v-chip
                                class="me-2 mb-2"
                                color="primary"
                                variant="outlined"
                            >
                                {{ set.total }} {{ $t('catalog.sets.cards_count') }}
                            </v-chip>
                            <v-chip
                                v-if="set.market_price"
                                class="me-2 mb-2"
                                color="success"
                                variant="outlined"
                            >
                                {{ formatPrice(set.market_price) }}
                            </v-chip>
                        </div>
                        
                        <div class="d-flex align-center">
                            <v-tabs
                                v-model="activeTab"
                                color="primary"
                                class="flex-grow-1"
                            >
                                <v-tab value="cards">{{ $t('catalog.cards.title') }}</v-tab>
                                <v-tab value="stats">{{ $t('ui.stats') }}</v-tab>
                            </v-tabs>
                            
                            <v-btn-toggle
                                v-show="activeTab === 'cards'"
                                v-model="viewMode"
                                color="primary"
                                density="comfortable"
                                class="ms-2"
                            >
                                <v-btn value="grid" icon="mdi-view-grid"></v-btn>
                                <v-btn value="list" icon="mdi-view-list"></v-btn>
                            </v-btn-toggle>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <v-window v-model="activeTab">
            <!-- Karty -->
            <v-window-item value="cards">
                <!-- Filtry a řazení -->
                <v-card class="mb-4">
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" sm="6" md="3" lg="3">
                                <v-text-field
                                    v-model="filters.search"
                                    :label="$t('catalog.filters.search')"
                                    prepend-inner-icon="mdi-magnify"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    clearable
                                    @update:model-value="updateFilters"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3" lg="3">
                                <v-select
                                    v-model="filters.type"
                                    :items="typeOptions"
                                    :label="$t('catalog.filters.type')"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    clearable
                                    @update:model-value="updateFilters"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3" lg="3">
                                <v-select
                                    v-model="filters.rarity"
                                    :items="rarityOptions"
                                    :label="$t('catalog.filters.rarity')"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    clearable
                                    @update:model-value="updateFilters"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3" lg="3">
                                <v-select
                                    v-model="sortOption"
                                    :items="sortOptions"
                                    :label="$t('catalog.filters.sort')"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details
                                    @update:model-value="updateSort"
                                />
                            </v-col>
                        </v-row>

                        <!-- Aktivní filtry a reset -->
                        <v-row v-if="hasActiveFilters" class="mt-2">
                            <v-col cols="12" class="d-flex align-center">
                                <div class="text-caption text-grey me-4">{{ $t('catalog.filters.active') }}</div>
                                <v-chip
                                    v-if="filters.search"
                                    class="me-2"
                                    closable
                                    @click:close="clearSearch"
                                >
                                    {{ $t('catalog.filters.search') }}: {{ filters.search }}
                                </v-chip>
                                <v-chip
                                    v-if="filters.type"
                                    class="me-2"
                                    closable
                                    @click:close="clearType"
                                >
                                    {{ $t('catalog.filters.type') }}: {{ filters.type }}
                                </v-chip>
                                <v-chip
                                    v-if="filters.rarity"
                                    class="me-2"
                                    closable
                                    @click:close="clearRarity"
                                >
                                    {{ $t('catalog.filters.rarity') }}: {{ filters.rarity }}
                                </v-chip>
                                <v-spacer />
                                <v-btn
                                    color="primary"
                                    variant="text"
                                    prepend-icon="mdi-refresh"
                                    @click="resetFilters"
                                >
                                    {{ $t('catalog.filters.reset') }}
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>

                <!-- Loading stav -->
                <v-row v-if="loading" justify="center" align="center" style="min-height: 400px">
                    <v-col cols="12" class="text-center">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                        />
                    </v-col>
                </v-row>

                <!-- Chybová hláška -->
                <v-row v-else-if="error || !hasCards" justify="center" align="center" style="min-height: 400px">
                    <v-col cols="12" md="8" lg="6">
                        <v-alert
                            type="error"
                            variant="tonal"
                            class="text-center"
                        >
                            {{ error || $t('catalog.cards.no_cards') }}
                        </v-alert>
                    </v-col>
                </v-row>

                <!-- Zobrazení karet - mřížka -->
                <v-row v-else-if="viewMode === 'grid'">
                    <v-col 
                        v-for="card in filteredCards" 
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
                            <v-img
                                :src="getCardImageUrl(card)"
                                :alt="card.name"
                                :aspect-ratio="0.716"
                                cover
                                class="rounded-sm card-img"
                                @error="$event.target.src = '/images/placeholder.jpg'"
                            >
                                <template v-slot:placeholder>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-progress-circular
                                            indeterminate
                                            color="grey-lighten-5"
                                            size="30"
                                        />
                                    </v-row>
                                </template>
                                <template v-slot:error>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-icon
                                            size="large"
                                            color="grey-lighten-1"
                                        >
                                            mdi-image-off
                                        </v-icon>
                                    </v-row>
                                </template>
                                <div class="card-number">{{ formatCardNumber(card.number) }}/{{ formatTotalNumber(set.printed_total) }}</div>
                            </v-img>

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
                </v-row>

                <!-- Zobrazení karet - seznam -->
                <v-card v-else-if="viewMode === 'list'" class="mb-4">
                    <v-data-table
                        v-model:items-per-page="itemsPerPage"
                        :headers="tableHeaders"
                        :items="filteredCards"
                        :items-per-page-options="[10, 20, 50, 100]"
                        item-value="id"
                        return-object
                        hover
                        density="compact"
                        show-select
                        multi-sort
                        class="elevation-1"
                    >
                        <template #[`item.image`]="{ item }">
                            <v-avatar size="30" rounded>
                                <v-img
                                    :src="getCardImageUrl(item)"
                                    :alt="item.name"
                                    :lazy-src="'/images/placeholder.jpg'"
                                >
                                    <template v-slot:placeholder>
                                        <v-progress-circular
                                            indeterminate
                                            color="grey-lighten-5"
                                            size="15"
                                        />
                                    </template>
                                    <template v-slot:error>
                                        <v-icon
                                            size="x-small"
                                            color="grey-lighten-1"
                                        >
                                            mdi-image-off
                                        </v-icon>
                                    </template>
                                </v-img>
                            </v-avatar>
                        </template>
                        <template #[`item.name`]="{ item }">
                            <Link :href="`/cards/${item.id}`" class="text-decoration-none">
                                {{ item.name }}
                            </Link>
                        </template>
                        <template #[`item.types`]="{ item }">
                            <v-icon 
                                v-if="item.types && item.types.length > 0"
                                :icon="getTypeIcon(item.types[0])"
                                :color="getTypeColor(item.types[0])"
                                size="small"
                                class="me-1"
                            />
                            <span v-if="item.types && item.types.length > 0">
                                {{ item.types[0] }}
                            </span>
                            <span v-else>-</span>
                        </template>
                        <template #[`item.rarity`]="{ item }">
                            <span :class="getRarityClass(item.rarity)">
                                {{ item.rarity }}
                            </span>
                        </template>
                        <template #[`item.number`]="{ item }">
                            {{ formatCardNumber(item.number) }}/{{ formatTotalNumber(set.printed_total) }}
                        </template>
                        <template #[`item.price`]="{ item }">
                            <div v-if="item.prices_cm && item.prices_cm.avg30 !== null" class="price-tag">
                                <v-tooltip location="bottom">
                                    <template v-slot:activator="{ props }">
                                        <span v-bind="props">
                                            {{ formatNumberPrice(item.prices_cm.avg30) }}
                                            <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                                        </span>
                                    </template>
                                    <div>
                                        <div>Aktualizace dne: {{ formatUpdateDate(item.prices_cm.updated_at) }}</div>
                                        <div class="d-flex flex-column mt-1">
                                            <div v-if="item.prices_cm.avg30 !== null" class="text-caption">
                                                Cena (normální): {{ formatPrice(item.prices_cm.avg30) }}
                                            </div>
                                            <div v-if="item.prices_cm.reverse_holo_avg30 !== null" class="text-caption">
                                                Cena (reverse holo): {{ formatPrice(item.prices_cm.reverse_holo_avg30) }}
                                            </div>
                                            <div class="text-caption mt-1">ID: {{ item.prices_cm.card_id }}</div>
                                        </div>
                                    </div>
                                </v-tooltip>
                            </div>
                            <div v-else>-</div>
                        </template>
                    </v-data-table>
                </v-card>
            </v-window-item>

            <!-- Statistiky -->
            <v-window-item value="stats">
                <v-card>
                    <v-card-text>
                        <div class="text-h6 mb-4">{{ $t('ui.stats') }}</div>
                        <p class="text-body-1">{{ $t('ui.coming_soon') }}</p>
                    </v-card-text>
                </v-card>
            </v-window-item>
        </v-window>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';

const props = defineProps({
    set: Object,
});

const page = usePage();

// Stav stránky
const loading = ref(false);
const error = ref(null);
const activeTab = ref('cards');
const viewMode = ref('grid');
const itemsPerPage = ref(50);
const filters = ref({
    search: '',
    type: '',
    rarity: '',
});

// Filtry
const typeOptions = [
    { title: page.props.translations?.catalog?.types?.all || 'Všechny typy', value: '' },
    { title: page.props.translations?.catalog?.types?.pokemon || 'Pokémon', value: 'Pokémon' },
    { title: page.props.translations?.catalog?.types?.trainer || 'Trenér', value: 'Trainer' },
    { title: page.props.translations?.catalog?.types?.energy || 'Energie', value: 'Energy' },
];

const rarityOptions = computed(() => {
    // Výchozí položka "Všechny vzácnosti"
    const rarityOpts = [{ title: page.props.translations?.catalog?.rarities?.all || 'Všechny vzácnosti', value: '' }];
    
    // Pokud nemáme set nebo karty, vrátíme jen výchozí položku
    if (!hasCards.value) return rarityOpts;
    
    // Získáme všechny unikátní vzácnosti z karet v setu
    const uniqueRarities = [...new Set(props.set.cards
        .map(card => card.rarity)
        .filter(rarity => rarity && rarity.trim() !== '')
    )];
    
    // Seřadíme vzácnosti podle určitého pořadí (common, uncommon, rare, ...)
    uniqueRarities.sort((a, b) => {
        const rarityOrder = {
            'Common': 1,
            'Uncommon': 2,
            'Rare': 3,
            'Rare Holo': 4,
            'Rare Ultra': 5,
            'Rare Secret': 6,
            'Amazing Rare': 7,
            'Ultra Rare': 8,
            'Secret Rare': 9,
            'Promo': 10
        };
        
        // Pokud vzácnost není v predefinovaném pořadí, dáme ji na konec
        const orderA = rarityOrder[a] || 100;
        const orderB = rarityOrder[b] || 100;
        
        return orderA - orderB;
    });
    
    // Přidáme unikátní vzácnosti do seznamu možností
    uniqueRarities.forEach(rarity => {
        rarityOpts.push({ title: rarity, value: rarity });
    });
    
    return rarityOpts;
});

const tableHeaders = [
    { title: '', key: 'image', sortable: false, width: '50px' },
    { title: page.props.translations?.catalog?.cards?.number || 'Číslo', key: 'number', width: '80px', sortable: true },
    { title: page.props.translations?.catalog?.cards?.name || 'Název karty', key: 'name', sortable: true },
    { title: page.props.translations?.catalog?.cards?.type || 'Typ', key: 'types', width: '120px', sortable: true },
    { title: page.props.translations?.catalog?.cards?.rarity || 'Vzácnost', key: 'rarity', width: '100px', sortable: true },
    { title: page.props.translations?.catalog?.cards?.price || 'Cena', key: 'price', width: '80px', align: 'end', sortable: true },
];

// Computed properties
const hasCards = computed(() => props.set && props.set.cards && props.set.cards.length > 0);
const hasActiveFilters = computed(() => filters.value.search || filters.value.type || filters.value.rarity);

const sortOption = ref('number_asc'); // Výchozí řazení podle čísla vzestupně

// Možnosti řazení
const sortOptions = [
    { title: page.props.translations?.catalog?.filters?.sort_options?.number_asc || 'Číslo (vzestupně)', value: 'number_asc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.number_desc || 'Číslo (sestupně)', value: 'number_desc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.name_asc || 'Název (A-Z)', value: 'name_asc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.name_desc || 'Název (Z-A)', value: 'name_desc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.price_asc || 'Cena (nejnižší)', value: 'price_asc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.price_desc || 'Cena (nejvyšší)', value: 'price_desc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.rarity_asc || 'Vzácnost (nejnižší)', value: 'rarity_asc' },
    { title: page.props.translations?.catalog?.filters?.sort_options?.rarity_desc || 'Vzácnost (nejvyšší)', value: 'rarity_desc' },
];

const filteredCards = computed(() => {
    if (!hasCards.value) return [];
    
    // Filtrování karet podle kritérií
    let result = props.set.cards.filter(card => {
        // Filtr podle názvu a/nebo čísla
        if (filters.value.search) {
            const searchLower = filters.value.search.toLowerCase();
            const cardNameLower = card.name.toLowerCase();
            const cardNumber = card.number.toString();
            
            // Rozdělíme hledaný výraz na slova
            const searchParts = searchLower.split(/\s+/);
            
            // Pokud je jedno slovo, hledáme v názvu NEBO čísle
            if (searchParts.length === 1) {
                const isInName = cardNameLower.includes(searchLower);
                const isInNumber = cardNumber.includes(searchLower);
                
                if (!isInName && !isInNumber) {
                    return false;
                }
            } 
            // Pokud jsou 2+ slova, zkusíme interpretovat jako "název číslo" nebo jako víceslovný název
            else {
                // Zjistíme, zda poslední slovo vypadá jako číslo
                const lastPart = searchParts[searchParts.length - 1];
                const isLastPartNumber = /^\d+$/.test(lastPart);
                
                if (isLastPartNumber) {
                    // Formát "název číslo" - název musí být v názvu karty a číslo musí odpovídat
                    const nameSearchParts = searchParts.slice(0, -1);
                    const nameSearch = nameSearchParts.join(' ');
                    
                    // Kontrola, zda název obsahuje hledaný text a číslo karty obsahuje hledané číslo
                    const isNameMatched = cardNameLower.includes(nameSearch);
                    const isNumberMatched = cardNumber.includes(lastPart);
                    
                    if (!(isNameMatched && isNumberMatched)) {
                        return false;
                    }
                } else {
                    // Jedná se o víceslovný název - všechna slova musí být v názvu
                    if (!searchParts.every(part => cardNameLower.includes(part))) {
                        return false;
                    }
                }
            }
        }
        
        // Filtr podle typu
        if (filters.value.type && card.supertype !== filters.value.type) {
            return false;
        }
        
        // Filtr podle vzácnosti
        if (filters.value.rarity && card.rarity !== filters.value.rarity) {
            return false;
        }
        
        return true;
    });
    
    // Řazení karet podle vybrané možnosti
    result.sort((a, b) => {
        // Pomocné funkce pro extrakci číselných hodnot
        const getNumericPart = (str) => Number(String(str).replace(/\D/g, '')) || 0;
        const getPrice = (card) => card.prices_cm?.avg30 || 0;
        
        // Řazení podle vzácnosti
        const rarityOrder = {
            'Common': 1,
            'Uncommon': 2,
            'Rare': 3,
            'Rare Holo': 4,
            'Rare Ultra': 5,
            'Rare Secret': 6,
            'Amazing Rare': 7,
            'Ultra Rare': 8,
            'Secret Rare': 9,
            'Promo': 10
        };
        
        const getRarityValue = (card) => rarityOrder[card.rarity] || 100;
        
        // Aplikace řazení
        switch (sortOption.value) {
            case 'number_asc':
                return getNumericPart(a.number) - getNumericPart(b.number);
            case 'number_desc':
                return getNumericPart(b.number) - getNumericPart(a.number);
            case 'name_asc':
                return a.name.localeCompare(b.name);
            case 'name_desc':
                return b.name.localeCompare(a.name);
            case 'price_asc':
                return getPrice(a) - getPrice(b);
            case 'price_desc':
                return getPrice(b) - getPrice(a);
            case 'rarity_asc':
                return getRarityValue(a) - getRarityValue(b);
            case 'rarity_desc':
                return getRarityValue(b) - getRarityValue(a);
            default:
                return getNumericPart(a.number) - getNumericPart(b.number);
        }
    });
    
    // Zajistíme, že vlastnosti jsou ve správném formátu
    return result.map(card => {
        // Pokud je types string (JSON), převedeme ho na pole
        if (typeof card.types === 'string') {
            try {
                card.types = JSON.parse(card.types);
            } catch (e) {
                card.types = card.types ? [card.types] : [];
            }
        }
        
        // Zajistíme, že types je pole
        if (!Array.isArray(card.types)) {
            card.types = card.types ? [card.types] : [];
        }
        
        return card;
    });
});

// Mounted hook pro diagnostiku
onMounted(() => {
    //console.log('Set data:', props.set);
    //console.log('Filtered cards:', filteredCards.value);
});

// Reset filtru při změně setu
watch(() => props.set, () => {
    filters.value.rarity = '';
}, { deep: true });

// Metody
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return format(date, 'd. MMMM yyyy', { locale: cs });
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

function updateFilters(resetPage = true) {
    // Zde by bylo volání API, pokud by filtrace probíhala na serveru
    // V našem případě jen aktualizujeme filtry a necháme computed property udělat svou práci
}

function clearSearch() {
    filters.value.search = '';
    updateFilters();
}

function clearType() {
    filters.value.type = '';
    updateFilters();
}

function clearRarity() {
    filters.value.rarity = '';
    updateFilters();
}

function resetFilters() {
    filters.value = {
        search: '',
        type: '',
        rarity: '',
    };
    updateFilters();
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

function checkImageExists(url) {
    // Jednoduchá funkce pro kontrolu existence obrázku
    // V reálné aplikaci by bylo lepší mít seznam existujících obrázků nebo použít API
    return true;
}

function getTypeIcon(type) {
    const icons = {
        'Colorless': 'mdi-circle-outline',
        'Darkness': 'mdi-moon-waning-crescent',
        'Dragon': 'mdi-dragon',
        'Fairy': 'mdi-butterfly',
        'Fighting': 'mdi-boxing-glove',
        'Fire': 'mdi-fire',
        'Grass': 'mdi-leaf',
        'Lightning': 'mdi-lightning-bolt',
        'Metal': 'mdi-anvil',
        'Psychic': 'mdi-eye',
        'Water': 'mdi-water',
    };
    
    return icons[type] || 'mdi-circle-outline';
}

function getTypeColor(type) {
    const colors = {
        'Colorless': 'grey',
        'Darkness': 'purple-darken-3',
        'Dragon': 'amber-darken-2',
        'Fairy': 'pink-lighten-2',
        'Fighting': 'brown',
        'Fire': 'red',
        'Grass': 'green',
        'Lightning': 'yellow-darken-2',
        'Metal': 'grey-darken-1',
        'Psychic': 'purple',
        'Water': 'blue',
    };
    
    return colors[type] || 'grey';
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

function formatTotalNumber(number) {
    if (!number) return '000';
    
    // Převedeme na string a zajistíme, že je číslo
    const numStr = String(number).replace(/\D/g, '');
    
    // Doplníme nuly zleva
    return numStr.padStart(3, '0');
}

function formatCardPrice(card) {
    // Kontrola, zda jsou k dispozici data o ceně
    if (!card || !card.prices_cm) {
        return '-';
    }
    
    // Získání ceny avg30 a reverse_holo_avg30 z prices_cm
    const regularPrice = card.prices_cm.avg30;
    const reverseHoloPrice = card.prices_cm.reverse_holo_avg30;
    
    // Zjistíme, který typ ceny použít a připravíme si označení typu
    let price = null;
    let priceType = '';
    
    if (regularPrice !== null && regularPrice !== undefined && reverseHoloPrice !== null && reverseHoloPrice !== undefined) {
        // Obě ceny jsou dostupné, použijeme vyšší a označíme typ
        if (regularPrice > reverseHoloPrice) {
            price = regularPrice;
            priceType = 'Regular';
        } else {
            price = reverseHoloPrice;
            priceType = 'Reverse Holo';
        }
    } else if (regularPrice !== null && regularPrice !== undefined) {
        // Pouze regularPrice je dostupný
        price = regularPrice;
        priceType = 'Regular';
    } else if (reverseHoloPrice !== null && reverseHoloPrice !== undefined) {
        // Pouze reverseHoloPrice je dostupný
        price = reverseHoloPrice;
        priceType = 'Reverse Holo';
    }
    
    if (price === null) {
        return '-';
    }
    
    // Formátování ceny v EUR
    const formattedPrice = new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
    
    // Pokud chceme zobrazit i typ ceny, můžeme vrátit objekt s oběma informacemi
    return { price: formattedPrice, type: priceType };
}

function formatShortPrice(price) {
    if (price === null || price === undefined || price === '-') {
        return '-';
    }
    
    // Pro zobrazení ve stručnějším formátu (pro karty)
    return price.toFixed(2) + ' €';
}

function formatUpdateDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return format(date, 'd. MMMM yyyy', { locale: cs });
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

function updateSort() {
    // Metoda pro aktualizaci řazení karet
    // V tuto chvíli není potřeba další logika, protože filteredCards je computed a reaguje na změny
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

.card-img {
    position: relative;
    overflow: hidden;
    background-color: #f5f5f5;
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

.series-title {
    position: relative;
    padding-bottom: 8px;
    margin-bottom: 16px;
}

.series-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    background: linear-gradient(to right, var(--v-primary-base), transparent);
}

.symbol-img {
    object-fit: contain;
}

.symbol-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
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

.set-header {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.set-logo-container {
    height: 150px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 24px);
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

/* Styly pro tabulku */
:deep(.v-data-table__tr:hover) {
    background-color: rgba(25, 118, 210, 0.04);
}

:deep(.v-data-table-footer) {
    border-top: thin solid rgba(0, 0, 0, 0.12);
    background-color: rgba(0, 0, 0, 0.02);
}
</style> 