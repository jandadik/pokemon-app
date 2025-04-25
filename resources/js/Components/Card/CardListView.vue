<template>
    <div>
        <!-- Filtry specifické pro list view -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6" md="3">
                        <v-text-field
                            v-model="cardStore.filters.search"
                            :label="$t('catalog.filters.search')"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            :loading="cardStore.isLoading"
                            @update:model-value="debouncedSearch"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-select
                            v-model="cardStore.filters.type"
                            :items="typeOptions"
                            :label="$t('catalog.filters.type')"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('type', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-select
                            v-model="cardStore.filters.rarity"
                            :items="rarityOptions"
                            :label="$t('catalog.filters.rarity')"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('rarity', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-select
                            v-model="cardStore.filters.set_id"
                            :items="setOptions"
                            :label="$t('catalog.filters.set')"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilter('set_id', $event)"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                        <v-select
                            v-model="cardStore.filters.per_page"
                            :items="[30, 60, 120]"
                            :label="$t('catalog.filters.per_page')"
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
                        <div class="text-caption text-grey me-4">{{ $t('catalog.filters.active_filters') }}</div>
                        <v-chip
                            v-if="cardStore.filters.search"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('search')"
                        >
                            {{ $t('catalog.filters.search') }}: {{ cardStore.filters.search }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.filters.type"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('type')"
                        >
                            {{ $t('catalog.filters.type') }}: {{ cardStore.filters.type }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.filters.rarity"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('rarity')"
                        >
                            {{ $t('catalog.filters.rarity') }}: {{ cardStore.filters.rarity }}
                        </v-chip>
                        <v-chip
                            v-if="cardStore.filters.set_id"
                            class="me-2 mb-1"
                            closable
                            @click:close="clearFilter('set_id')"
                        >
                            {{ $t('catalog.filters.set') }}: {{ getSetName(cardStore.filters.set_id) }}
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

        <!-- Zobrazení karet - seznam -->
        <v-card v-if="hasCards" class="mb-4">
            <v-data-table
                :headers="tableHeaders"
                :items="cards.data"
                :items-per-page="cardStore.filters.per_page"
                :page="cardStore.filters.page"
                :sort-by="dataTableSortBy"
                :server-items-length="cards.total"
                :loading="cardStore.isLoading"
                @update:options="updateTableOptionsInStore"
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
                            v-if="cardStore.isLoading"
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
                <template #[`item.price_cm_avg30`]="{ item }">
                    <div v-if="item.price_cm_avg30 !== null || item.price_cm_trend !== null || item.price_tcg_market !== null" class="price-tag">
                        <v-tooltip location="bottom">
                            <template v-slot:activator="{ props }">
                                <span v-bind="props">
                                    {{ getPriceValue(item) }}
                                    <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                                </span>
                            </template>
                            <div>
                                <div v-if="item.price_cm_updated_at">
                                    Aktualizace CM: {{ formatUpdateDate(item.price_cm_updated_at) }}
                                </div>
                                <div class="d-flex flex-column mt-1">
                                    <div v-if="item.price_cm_avg30 !== null" class="text-caption">
                                        Cardmarket Avg30: {{ formatPrice(item.price_cm_avg30) }}
                                    </div>
                                    <div v-if="item.price_cm_trend !== null" class="text-caption">
                                        Cardmarket Trend: {{ formatPrice(item.price_cm_trend) }}
                                    </div>
                                    <div v-if="item.price_tcg_market !== null" class="text-caption mt-1">
                                        TCGPlayer Market: {{ formatNumberPrice(item.price_tcg_market) }} USD
                                    </div>
                                    <div v-if="item.price_tcg_updated_at">
                                        Aktualizace TCG: {{ formatUpdateDate(item.price_tcg_updated_at) }}
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
                    :model-value="cardStore.filters.page"
                    :length="paginationLength"
                    :total-visible="7"
                    @update:model-value="setPageInStore"
                    rounded
                    :disabled="cardStore.isLoading"
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
import { computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';
import { debounce } from 'lodash';
import { useCardStore } from '@/stores/cardStore';
// Import sdílených utilit
import {
    getCardImageUrl,
    getTypeIcon,
    getTypeColor,
    getRarityClass,
    formatCardNumber,
    formatNumberPrice,
    formatPrice,
    formatUpdateDate,
    handleImageError,
    getPriceValue
} from '@/composables/useCardUtils';

const page = usePage();

const cardStore = useCardStore();

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

const typeOptions = computed(() => [
    { title: page.props.translations?.catalog?.filters?.all_types || 'Všechny typy', value: '' },
    { title: page.props.translations?.catalog?.types?.Pokémon || 'Pokémon', value: 'Pokémon' },
    { title: page.props.translations?.catalog?.types?.Trainer || 'Trenér', value: 'Trainer' },
    { title: page.props.translations?.catalog?.types?.Energy || 'Energie', value: 'Energy' },
]);

const rarityOptions = computed(() => [
    { title: page.props.translations?.catalog?.filters?.all_rarities || 'Všechny vzácnosti', value: '' },
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
]);

const setOptions = computed(() => {
    const options = [{ 
        title: page.props.translations?.catalog?.filters?.all_sets || 'Všechny sety', 
        value: '' 
    }];
    
    if (page.props.sets && page.props.sets.length > 0) {
        const sortedSets = [...page.props.sets].sort((a, b) => a.name.localeCompare(b.name));
        
        sortedSets.forEach(set => {
            options.push({ title: set.name, value: set.id });
        });
    }
    
    return options;
});

const tableHeaders = computed(() => [
    { title: '', key: 'image', sortable: false, width: '50px' },
    { title: page.props.translations?.catalog?.table?.headers?.name || 'Název karty', key: 'name', sortable: true },
    { title: page.props.translations?.catalog?.table?.headers?.set || 'Set', key: 'set.name', width: '250px', sortable: true },
    { title: page.props.translations?.catalog?.table?.headers?.number || 'Číslo', key: 'number', width: '80px', sortable: true },
    { title: page.props.translations?.catalog?.table?.headers?.rarity || 'Vzácnost', key: 'rarity', width: '220px', sortable: true },
    { title: page.props.translations?.catalog?.table?.headers?.price_avg30 || 'Cena (Avg30)', key: 'price_cm_avg30', width: '110px', align: 'end', sortable: true },
]);

const hasCards = computed(() => props.cards && props.cards.data && props.cards.data.length > 0);
const hasActiveFilters = computed(() => 
    cardStore.filters.search || 
    cardStore.filters.type || 
    cardStore.filters.rarity || 
    cardStore.filters.set_id
);
const paginationLength = computed(() => 
    Math.ceil((props.cards?.total || 0) / (cardStore.filters.per_page || 30))
);
const dataTableSortBy = computed(() => {
    return [{ 
        key: cardStore.filters.sort_by, 
        order: cardStore.filters.sort_direction === 'desc' ? 'desc' : 'asc' 
    }];
});

const debouncedSearch = debounce((value) => {
    cardStore.setSearch(value ?? '');
}, 500);

function updateFilter(key, value) {
    const newValue = value ?? '';
    switch (key) {
        case 'type':
            cardStore.setType(newValue);
            break;
        case 'rarity':
            cardStore.setRarity(newValue);
            break;
        case 'set_id':
            cardStore.setSetId(newValue);
            break;
        case 'per_page':
            cardStore.setPerPage(newValue || 30);
            break;
    }
}

function clearFilter(key) {
    switch (key) {
        case 'search':
            cardStore.setSearch('');
            break;
        case 'type':
            cardStore.setType('');
            break;
        case 'rarity':
            cardStore.setRarity('');
            break;
        case 'set_id':
            cardStore.setSetId('');
            break;
    }
}

function resetFilters() {
    cardStore.resetFilters();
}

function updateTableOptionsInStore(options) {
    // console.log('updateTableOptionsInStore - options:', JSON.parse(JSON.stringify(options)));
    // console.log('updateTableOptionsInStore - current filters:', JSON.parse(JSON.stringify(cardStore.filters)));

    if (options.sortBy && options.sortBy.length > 0) {
        const sortInfo = options.sortBy[0];
        let newSortKey = sortInfo.key;
        const newSortOrder = sortInfo.order === 'desc' ? 'desc' : 'asc';

        // Mapování klíčů z v-data-table na klíče pro backend/store
        if (newSortKey === 'price_cm_avg30') { 
            newSortKey = 'cm_avg30';
        } else if (newSortKey === 'set.name') {
            // Klíč 'set.name' je již správný pro backend
            // newSortKey = 'set.name'; // ponechat nebo odstranit, je to redundantní
        }

        if (newSortKey !== cardStore.filters.sort_by || newSortOrder !== cardStore.filters.sort_direction) {
            const sortValue = `${newSortKey}_${newSortOrder}`;
            // console.log(` -> Změna řazení, volám setSortOption("${sortValue}")`);
            cardStore.setSortOption(sortValue);
            return;
        }
    }

    if (options.itemsPerPage && options.itemsPerPage !== cardStore.filters.per_page) {
        // console.log(` -> Změna počtu položek, volám setPerPage(${options.itemsPerPage})`);
        cardStore.setPerPage(options.itemsPerPage);
        return;
    }

    if (options.page && options.page !== cardStore.filters.page) {
        // console.log(` -> Změna stránky, volám setPage(${options.page})`);
        cardStore.setPage(options.page);
        return;
    }

    // console.log('updateTableOptionsInStore - žádná relevantní změna');
}

function setPageInStore(newPage) {
    cardStore.setPage(newPage);
}

function getSetName(setId) {
    if (!setId || !page.props.sets) return '';
    const set = page.props.sets.find(s => s.id === setId);
    return set ? set.name : '';
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

:deep(.v-data-table__tr:hover) {
    background-color: rgba(25, 118, 210, 0.04);
}

:deep(.v-data-table-footer) {
    border-top: thin solid rgba(0, 0, 0, 0.12);
    background-color: rgba(0, 0, 0, 0.02);
}

:deep(.v-pagination__item--is-active) {
    font-weight: bold;
}

.v-skeleton-loader {
    border-radius: 4px;
}
</style> 