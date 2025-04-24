<template>
    <div>
        <!-- Filtry specifické pro list view -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3">
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
                    <v-col cols="12" sm="6" md="2">
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
                    <v-col cols="12" sm="6" md="2">
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
                    <v-col cols="12" sm="6" md="2">
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
                    <v-col cols="12" sm="6" md="2">
                        <v-select
                            v-model="localFilters.per_page"
                            :items="[30, 60, 120]"
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

        <!-- Zobrazení karet - seznam -->
        <v-card v-if="hasCards" class="mb-4">
            <v-data-table
                :headers="tableHeaders"
                :items="cards.data"
                :items-per-page="localFilters.per_page"
                :server-items-length="cards.total"
                :loading="isLoading"
                v-model:options="tableOptions"
                @update:options="updateOptions"
                item-value="id"
                hover
                density="compact"
                class="elevation-1"
                must-sort
                return-object
                hide-default-footer
            >
                <template #[`item.image`]="{ item }">
                    <div class="list-img-container">
                        <v-skeleton-loader
                            v-if="isLoading"
                            type="image"
                            width="30"
                            height="30"
                        />
                        <img 
                            v-else
                            :src="getCardImageUrl(item)" 
                            :alt="item.name" 
                            width="30" 
                            height="30"
                            class="list-img"
                            @error="handleImageError($event)"
                        >
                    </div>
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
                <template #[`item.set`]="{ item }">
                    <span v-if="item.set">{{ item.set.name }}</span>
                    <span v-else>-</span>
                </template>
                <template #[`item.number`]="{ item }">
                    {{ formatCardNumber(item.number) }}
                </template>
                <template #[`item.price`]="{ item }">
                    <div v-if="item.prices_cm || item.price || item.prices" class="price-tag">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <span v-bind="props">
                                    {{ getPriceValue(item) }}
                                    <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                                </span>
                            </template>
                            <div>
                                <div v-if="item.prices_cm && item.prices_cm.updated_at">
                                    Aktualizace dne: {{ formatUpdateDate(item.prices_cm.updated_at) }}
                                </div>
                                <div class="d-flex flex-column mt-1">
                                    <div v-if="item.prices_cm && item.prices_cm.avg30 !== null" class="text-caption">
                                        Cena (normální): {{ formatPrice(item.prices_cm.avg30) }}
                                    </div>
                                    <div v-if="item.prices_cm && item.prices_cm.reverse_holo_avg30 !== null" class="text-caption">
                                        Cena (reverse holo): {{ formatPrice(item.prices_cm.reverse_holo_avg30) }}
                                    </div>
                                    <div v-if="item.prices_cm && item.prices_cm.card_id" class="text-caption mt-1">
                                        ID: {{ item.prices_cm.card_id }}
                                    </div>
                                </div>
                            </div>
                        </v-tooltip>
                    </div>
                    <div v-else>-</div>
                </template>
            </v-data-table>
            
            <!-- Externí paginace -->
            <div class="d-flex justify-center mt-4">
                <v-pagination
                    v-model="currentPage"
                    :length="Math.ceil(cards.total / localFilters.per_page)"
                    :total-visible="7"
                    @update:model-value="updatePage"
                    rounded
                    :disabled="isLoading"
                ></v-pagination>
            </div>
        </v-card>

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
import { ref, computed, watch, onMounted, reactive, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';
import { debounce } from 'lodash';

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

// Lokální filtry pro ListView
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

// Nastavení tabulky
const tableOptions = ref({
    page: currentPage.value,
    itemsPerPage: localFilters.per_page,
    sortBy: [{ key: localFilters.sort_by, order: localFilters.sort_direction === 'desc' ? 'desc' : 'asc' }],
    multiSort: false,
    mustSort: true,
    sortDesc: localFilters.sort_direction === 'desc',
});

// Možnosti filtrů
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

// Tabulkové záhlaví
const tableHeaders = [
    { title: '', key: 'image', sortable: false, width: '50px' },
    { title: 'Název karty', key: 'name', sortable: true },
    { title: 'Set', key: 'set', width: '250px', sortable: true },
    { title: 'Číslo', key: 'number', width: '80px', sortable: true },
    { title: 'Typ', key: 'types', width: '120px', sortable: true },
    { title: 'Vzácnost', key: 'rarity', width: '220px', sortable: true },
    { title: 'Cena', key: 'price', width: '80px', align: 'end', sortable: true },
];

// Počítané vlastnosti
const hasCards = computed(() => props.cards && props.cards.data && props.cards.data.length > 0);
const hasActiveFilters = computed(() => localFilters.search || localFilters.type || localFilters.rarity || localFilters.set_id);

// Sledování změn props
watch(() => props.cards, (newCards) => {
    if (newCards?.current_page && newCards.current_page !== currentPage.value) {
        currentPage.value = newCards.current_page;
        
        // Aktualizujeme i tableOptions
        tableOptions.value = {
            ...tableOptions.value,
            page: newCards.current_page
        };
    }
}, { immediate: true, deep: true });

watch(() => props.filters, (newFilters) => {
    // Aktualizujeme lokální filtry podle změn v props
    Object.keys(localFilters).forEach(key => {
        if (newFilters[key] !== undefined) {
            localFilters[key] = newFilters[key];
        }
    });
    
    // Aktualizujeme tableOptions, pokud se změnilo řazení nebo stránkování
    if (newFilters.page && parseInt(newFilters.page) !== currentPage.value) {
        currentPage.value = parseInt(newFilters.page);
        tableOptions.value.page = parseInt(newFilters.page);
    }
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

// Odpojení sledování událostí při unmount
onUnmounted(() => {
    try {
        router.off('start');
        router.off('finish');
    } catch (e) {
        console.debug('Nelze odpojit router event, ignorujeme:', e);
    }
});

// Metody pro filtry
function updateFilter(key, value) {
    localFilters[key] = value;
    applyFilters(true);
}

function clearFilter(key) {
    localFilters[key] = '';
    applyFilters(true);
}

function resetFilters() {
    Object.keys(localFilters).forEach(key => {
        if (key !== 'page' && key !== 'per_page' && key !== 'sort_by' && key !== 'sort_direction') {
            localFilters[key] = '';
        }
    });
    localFilters.page = 1;
    localFilters.per_page = 30;
    localFilters.sort_by = 'name';
    localFilters.sort_direction = 'asc';
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

// Metoda pro v-data-table
function updateOptions(options) {
    if (options.page !== currentPage.value) {
        currentPage.value = options.page;
    }
    
    if (options.itemsPerPage !== localFilters.per_page) {
        localFilters.per_page = options.itemsPerPage;
    }
    
    if (options.sortBy && options.sortBy.length > 0) {
        const sortInfo = options.sortBy[0];
        localFilters.sort_by = sortInfo.key;
        localFilters.sort_direction = sortInfo.order;
    }
    
    tableOptions.value = { ...options };
    
    // Vytvoříme nový objekt filtrů
    const newFilters = { ...localFilters, page: currentPage.value };
    
    // Informujeme rodičovskou komponentu o změně filtrů
    emit('update:filters', newFilters);
    
    // Odešleme požadavek na server
    router.get('/cards', newFilters, {
        preserveState: true,
        preserveScroll: true,
        only: ['cards']
    });
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

function updatePage(newPage) {
    currentPage.value = newPage;
    applyFilters(false);
}

function handleImageError(event) {
    event.target.src = '/images/placeholder.jpg';
}

function getPriceValue(item) {
    // Zkontrolujte všechny možné varianty ceny
    if (item.prices_cm && item.prices_cm.avg30 !== null) {
        return formatNumberPrice(item.prices_cm.avg30);
    } else if (item.price) {
        return formatNumberPrice(item.price);
    } else if (item.prices && item.prices.avg) {
        return formatNumberPrice(item.prices.avg);
    }
    return '-';
}
</script>

<style scoped>
.list-img-container {
    width: 30px;
    height: 30px;
    overflow: hidden;
    border-radius: 4px;
}

.list-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 4px;
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

/* Styly pro tabulku */
:deep(.v-data-table__tr:hover) {
    background-color: rgba(25, 118, 210, 0.04);
}

:deep(.v-data-table-footer) {
    border-top: thin solid rgba(0, 0, 0, 0.12);
    background-color: rgba(0, 0, 0, 0.02);
}

/* Styly pro stránkování */
:deep(.v-pagination__item--is-active) {
    font-weight: bold;
}

/* Přidání stylů pro loading stav */
.v-skeleton-loader {
    border-radius: 4px;
}
</style> 