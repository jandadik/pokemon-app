<template>
    <v-container fluid>
        <!-- Filtry a řazení -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6" lg="4">
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
                    <v-col cols="12" md="6" lg="4">
                        <v-select
                            v-model="filters.series"
                            :items="seriesList"
                            :label="$t('catalog.filters.series')"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            clearable
                            @update:model-value="updateFilters"
                        />
                    </v-col>
                    <v-col cols="12" md="6" lg="4">
                        <v-select
                            v-model="selectedSortIndex"
                            :items="sortOptions.map((item, index) => ({
                                title: item.title,
                                value: index
                            }))"
                            :label="$t('catalog.filters.sort')"
                            variant="outlined"
                            density="comfortable"
                            hide-details
                            item-title="title"
                            item-value="value"
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
                            v-if="filters.series"
                            class="me-2"
                            closable
                            @click:close="clearSeries"
                        >
                            {{ $t('catalog.filters.series') }}: {{ filters.series }}
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
        <v-row v-else-if="error || !hasSets" justify="center" align="center" style="min-height: 400px">
            <v-col cols="12" md="8" lg="6">
                <v-alert
                    type="error"
                    variant="tonal"
                    class="text-center"
                >
                    {{ error || $t('catalog.sets.no_sets') }}
                </v-alert>
            </v-col>
        </v-row>

        <!-- Zobrazení setů -->
        <template v-else>
            <template v-for="(seriesSets, series) in groupedSets" :key="series">
                <!-- Nadpis série -->
                <v-row>
                    <v-col cols="12">
                        <h2 class="series-title">
                            {{ series }}
                        </h2>
                    </v-col>
                </v-row>

                <!-- Sety v sérii -->
                <v-row>
                    <v-col 
                        v-for="set in seriesSets" 
                        :key="set.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                        class="mb-4"
                    >
                        <v-card
                            @click="router.visit(`/sets/${set.id}`)"
                            class="h-100"
                            hover
                            style="cursor: pointer;"
                        >
                            <LazyImage
                                :src="set.logo_url"
                                :alt="set.name"
                                height="150"
                                max-width="85%"
                                contain
                                position="center center"
                                class="mx-auto px-6 py-3 rounded-sm set-logo-img"
                                @error="handleImageError"
                            />

                            <v-card-title class="pa-3">
                                <div class="d-flex w-100">
                                    <div class="symbol-wrapper me-2 flex-shrink-0">
                                        <img 
                                            v-if="set.ptcgo_code && checkImageExists(`/images/symbols/${set.ptcgo_code.toLowerCase()}.png`)"
                                            :src="`/images/symbols/${set.ptcgo_code.toLowerCase()}.png`" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                            @error="handleSymbolImageError(set)"
                                        />
                                        <img 
                                            v-else-if="set.ptcgo_code && checkImageExists(`/images/symbols/${set.ptcgo_code.toLowerCase()}.svg`)"
                                            :src="`/images/symbols/${set.ptcgo_code.toLowerCase()}.svg`" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                            @error="handleSymbolImageError(set)"
                                        />
                                        <img 
                                            v-else-if="set.symbol_url"
                                            :src="set.symbol_url" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                            @error="handleSymbolImageError(set)"
                                        />
                                        <v-icon 
                                            v-else
                                            icon="mdi-cards"
                                            size="24"
                                            class="symbol-img text-primary" 
                                        />
                                    </div>
                                    <div class="set-title flex-grow-1">
                                        {{ set.name }}
                                    </div>
                                </div>
                            </v-card-title>

                            <v-card-text>
                                <div class="d-flex justify-space-between align-center mb-2">
                                    <div class="text-caption text-grey">
                                        {{ formatDate(set.release_date) }}
                                    </div>
                                    <v-chip
                                        size="small"
                                        color="primary"
                                        variant="outlined"
                                        class="d-flex align-center"
                                    >
                                        {{ set.total }} {{ $t('catalog.sets.cards_count') }}
                                    </v-chip>
                                </div>
                                
                                <!-- Tržní cena setu -->
                                <div class="d-flex justify-space-between align-center mt-3">
                                    <div>
                                        <v-btn
                                            size="small"
                                            variant="tonal"
                                            color="primary"
                                            @click="router.visit(`/sets/${set.id}`)"
                                            class="text-uppercase text-caption px-3 me-2"
                                            style="min-width: auto;"
                                            prepend-icon="mdi-chart-box-outline"
                                        >
                                            {{ $t('catalog.sets.details') }}
                                        </v-btn>
                                        
                                        <v-btn
                                            size="small"
                                            variant="tonal"
                                            color="secondary"
                                            @click="router.visit(`/sets/${set.id}/cards`)"
                                            class="text-uppercase text-caption px-3"
                                            style="min-width: auto;"
                                            prepend-icon="mdi-cards"
                                        >
                                            {{ $t('catalog.cards.title') }}
                                        </v-btn>
                                    </div>
                                    
                                    <span v-if="set.market_price" class="text-success font-weight-medium">
                                        {{ formatPrice(set.market_price) }}
                                        <v-tooltip activator="parent" location="bottom">
                                            {{ $t('catalog.sets.market_price_tooltip') }}
                                        </v-tooltip>
                                    </span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Oddělovač mezi sériemi -->
                <v-divider class="my-6" />
            </template>
        </template>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import LazyImage from '@/Components/UI/LazyImage.vue'

const page = usePage();
const props = defineProps({
    sets: {
        type: Object,
        required: true,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        required: true,
        default: () => ({
            search: '',
            series: '',
            sort_by: 'release_date',
            sort_direction: 'desc'
        })
    },
    seriesList: {
        type: Array,
        required: true,
        default: () => []
    }
})

const loading = ref(false)
const error = ref(null)
const selectedSortIndex = ref(0)

// Možnosti řazení - nyní jako computed property
const sortOptions = computed(() => {
    return [
        { 
            title: page.props.translations?.catalog?.filters?.sort_options?.release_date_desc || 'Datum vydání (nejnovější)', 
            value: 'release_date', 
            direction: 'desc' 
        },
        { 
            title: page.props.translations?.catalog?.filters?.sort_options?.release_date_asc || 'Datum vydání (nejstarší)', 
            value: 'release_date', 
            direction: 'asc' 
        },
        { 
            title: page.props.translations?.catalog?.filters?.sort_options?.name_asc || 'Název (A-Z)', 
            value: 'name', 
            direction: 'asc' 
        },
        { 
            title: page.props.translations?.catalog?.filters?.sort_options?.name_desc || 'Název (Z-A)', 
            value: 'name', 
            direction: 'desc' 
        },
        { 
            title: page.props.translations?.catalog?.filters?.sort_options?.price_asc || 'Série', 
            value: 'series', 
            direction: 'asc' 
        }
    ];
});

// Aktuální filtry
const filters = ref({
    search: props.filters.search || '',
    series: props.filters.series || '',
    sort_by: props.filters.sort_by || 'release_date',
    sort_direction: props.filters.sort_direction || 'desc'
})

// Kontrola existence dat
const hasSets = computed(() => {
    return props.sets?.data?.length > 0
})

// Kontrola aktivních filtrů
const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.series
})

// Výpočet seskupených setů podle série
const groupedSets = computed(() => {
    if (!hasSets.value) return {}
    
    const grouped = {}
    
    props.sets.data.forEach(set => {
        if (!grouped[set.series]) {
            grouped[set.series] = []
        }
        grouped[set.series].push(set)
    })
    
    return grouped
})

// Cache pro kontrolu existence obrázků
const checkImageExists = (imagePath) => {
    // Pokud jsme již kontrolovali tento obrázek, vrátíme výsledek z cache
    return true; // Pro zjednodušení vždy vracíme true a případně zachytíme chybu při načítání
}

// Obsluha chyby načítání obrázku
const handleImageError = (error) => {
    console.error('Chyba při načítání obrázku:', error);
}

// Obsluha chyby načítání symbolu setu
const handleSymbolImageError = (set) => {
    console.error(`Chyba při načítání symbolu setu ${set.id}:`, set.ptcgo_code);
}

// Nastavení výchozího indexu řazení podle props
onMounted(() => {
    // Debug informace
    console.log('Props sets:', props.sets);
    console.log('Props sets data:', props.sets?.data);
    console.log('Props sets data length:', props.sets?.data?.length);
    console.log('hasSets computed:', hasSets.value);
    
    nextTick(() => {
        try {
            const sortByValue = props.filters.sort_by || 'release_date';
            const sortDirection = props.filters.sort_direction || 'desc';
            
            const index = sortOptions.value.findIndex(option => 
                option.value === sortByValue && option.direction === sortDirection
            );
            
            selectedSortIndex.value = index !== -1 ? index : 0;
        } catch (e) {
            console.error('Chyba při inicializaci řazení:', e);
            selectedSortIndex.value = 0;
        }
    });
});

// Aktualizace řazení
const updateSort = (index) => {
    const selectedOption = sortOptions.value[index]
    filters.value.sort_by = selectedOption.value
    filters.value.sort_direction = selectedOption.direction
    updateFilters()
}

// Aktualizace filtrů
const updateFilters = () => {
    // Najdeme vybranou možnost řazení
    const selectedSort = sortOptions.value.find(option => 
        option.value === filters.value.sort_by && option.direction === filters.value.sort_direction
    ) || sortOptions.value[0];

    router.get('/sets', {
        search: filters.value.search,
        series: filters.value.series,
        sort_by: selectedSort.value,
        sort_direction: selectedSort.direction
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

// Reset všech filtrů
const resetFilters = () => {
    filters.value = {
        search: '',
        series: '',
        sort_by: 'release_date',
        sort_direction: 'desc'
    }
    updateFilters()
}

// Vyčištění vyhledávání
const clearSearch = () => {
    filters.value.search = ''
    updateFilters()
}

// Vyčištění filtru série
const clearSeries = () => {
    filters.value.series = ''
    updateFilters()
}

// Formátování data
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('cs-CZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Formátování ceny
const formatPrice = (price) => {
    if (typeof price === 'number') {
        return price.toLocaleString('cs-CZ', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    } else if (typeof price === 'string') {
        return parseFloat(price).toLocaleString('cs-CZ', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    return '0,00 €';
}
</script>

<style lang="scss" scoped>
    .v-card-title {
        font-weight: 500;
        letter-spacing: 0.0125em;
        font-size: 1rem !important;
        line-height: 1.3;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .v-img {
        transition: transform 0.3s ease;
    }

    .v-card:hover .v-img {
        transform: scale(1.03);
    }

    .set-logo-img {
        background: transparent !important;
        border-bottom: 1px solid rgba(var(--v-theme-primary), 0.05);
    }

    .set-title {
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
        max-height: 2.6em; /* Přibližně 2 řádky */
        word-break: break-word;
        hyphens: auto;
        width: 100%;
        position: relative;
        
        /* Alternativní způsob "multi-line ellipsis" */
        &::after {
            content: "";
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30%; /* Gradient šířka pro výfade */
            height: 1.3em; /* Výška jednoho řádku */
            background: linear-gradient(to right, rgba(255, 255, 255, 0), var(--v-theme-surface) 80%);
            pointer-events: none;
        }
    }

    .series-title {
        position: relative;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        font-weight: 600;
    }

    .series-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(to right, rgba(var(--v-theme-primary), 0.7), rgba(var(--v-theme-primary), 0.1));
    }

    .symbol-img {
        object-fit: contain;
        filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.2));
    }

    .symbol-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        min-height: 24px;
    }
</style>