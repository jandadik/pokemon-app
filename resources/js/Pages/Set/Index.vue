<template>
    <v-container fluid>
        <!-- Filtry a řazení -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6" lg="4">
                        <v-text-field
                            v-model="filters.search"
                            label="Vyhledat set"
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
                            label="Série"
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
                            label="Řadit podle"
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
                        <div class="text-caption text-grey me-4">Aktivní filtry:</div>
                        <v-chip
                            v-if="filters.search"
                            class="me-2"
                            closable
                            @click:close="clearSearch"
                        >
                            Vyhledávání: {{ filters.search }}
                        </v-chip>
                        <v-chip
                            v-if="filters.series"
                            class="me-2"
                            closable
                            @click:close="clearSeries"
                        >
                            Série: {{ filters.series }}
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
                    {{ error || 'Nebyly nalezeny žádné sety karet.' }}
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
                            :to="`/sets/${set.id}`"
                            class="h-100"
                            hover
                        >
                            <v-img
                                :src="set.logo_url"
                                :alt="set.name"
                                height="200"
                                contain
                                position="center center"
                                class="bg-grey-darken-4 px-4 py-4"
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
                            </v-img>

                            <v-card-title class="d-flex align-center">
                                <div class="d-flex align-center w-100">
                                    <div class="symbol-wrapper me-2">
                                        <img 
                                            v-if="set.ptcgo_code"
                                            :src="`/images/symbols/${set.ptcgo_code.toLowerCase()}.png`" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                        />
                                        <img 
                                            v-else-if="set.symbol_url"
                                            :src="set.symbol_url" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                        />
                                        <img 
                                            v-else
                                            src="/images/pokeball.png" 
                                            :alt="set.name"
                                            class="symbol-img" 
                                            width="24"
                                            height="24"
                                        />
                                    </div>
                                    <div>
                                        <div class="text-truncate">{{ set.name }}</div>
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
                                        {{ set.total }} karet
                                    </v-chip>
                                </div>
                            </v-card-text>
                            
                            <v-card-actions>
                                <v-btn
                                    block
                                    variant="tonal"
                                    color="primary"
                                    :to="`/sets/${set.id}`"
                                >
                                    Zobrazit detail
                                </v-btn>
                            </v-card-actions>
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
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import SetCard from '@/Components/Set/SetCard.vue'

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

// Možnosti řazení
const sortOptions = [
    { title: 'Datum vydání (nejnovější)', value: 'release_date', direction: 'desc' },
    { title: 'Datum vydání (nejstarší)', value: 'release_date', direction: 'asc' },
    { title: 'Název (A-Z)', value: 'name', direction: 'asc' },
    { title: 'Název (Z-A)', value: 'name', direction: 'desc' },
    { title: 'Série', value: 'series', direction: 'asc' }
]

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

// Nastavení výchozího indexu řazení podle props
onMounted(() => {
    const sortByValue = props.filters.sort_by || 'release_date'
    const sortDirection = props.filters.sort_direction || 'desc'
    
    const index = sortOptions.findIndex(option => 
        option.value === sortByValue && option.direction === sortDirection
    )
    
    selectedSortIndex.value = index !== -1 ? index : 0
})

// Aktualizace řazení
const updateSort = (index) => {
    const selectedOption = sortOptions[index]
    filters.value.sort_by = selectedOption.value
    filters.value.sort_direction = selectedOption.direction
    updateFilters()
}

// Aktualizace filtrů
const updateFilters = () => {
    // Najdeme vybranou možnost řazení
    const selectedSort = sortOptions.find(option => 
        option.value === filters.value.sort_by && option.direction === filters.value.sort_direction
    ) || sortOptions[0];

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
</script>

<style lang="scss" scoped>
.v-card-title {
    font-weight: 500;
    letter-spacing: 0.0125em;
}

.v-img {
    transition: transform 0.3s ease;
}

.v-card:hover .v-img {
    transform: scale(1.03);
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

.v-img.bg-grey-darken-4 {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
</style>