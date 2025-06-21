<template>
    <v-container>
        <Head :title="collection ? collection.name : $t('collections.titles.detail')" />

        <!-- Hlavička kolekce -->
        <CollectionHeader
            :collection="collection"
            :can="can"
            :stats="stats"
            :show-stats="showStats"
            :is-setting-default="isSettingDefault"
            :is-toggling-visibility="isTogglingVisibility"
            @toggle-stats="toggleStats"
            @add-item="() => router.visit(route('collections.items.create', collection.id))"
            @edit-collection="() => router.visit(route('collections.edit', collection.id))"
            @set-default="() => setAsDefault(collection)"
            @toggle-visibility="() => toggleVisibility(collection)"
            @delete-collection="() => confirmDelete = true"
        />

        <!-- Statistiky kolekce -->
        <SkeletonLoader v-if="showStats && isLoadingStats" type="stats" />
        <CollectionStats v-else-if="collection && stats && showStats" :stats="stats" />

        <!-- Bulk Actions Toolbar -->
        <BulkActionsToolbar 
            v-if="hasSelectedItems"
            :selected-count="selectedItemsCount"
            :total-items="filteredItems.length"
            :selected-items="selectedItemsArray"
            @select-all="selectAllItemsHandler"
            @bulk-edit="() => handleBulkEdit(selectedItemsArray)"
            @bulk-duplicate="() => handleBulkDuplicate(selectedItemsArray, collection, clearSelection)"
            @bulk-export="() => handleBulkExport(selectedItemsArray, collection, clearSelection)"
            @bulk-delete="(items) => handleBulkDelete(items, collection, clearSelection)"
            @clear-selection="clearSelection"
        />

        <!-- Toolbar s filtry a ovládáním -->
        <CollectionToolbar
            :filter-options="filterOptions"
            :has-active-filters="hasActiveFilters"
            :bulk-mode="bulkMode"
            :view-mode="viewMode"
            :is-loading-data="isLoadingData"
            @reset-filters="resetFilters"
            @toggle-bulk-mode="toggleBulkMode"
            @update:view-mode="(mode) => collectionsStore.setViewMode(mode)"
        />

        <!-- Zobrazení karet -->
        <div v-if="isLoadingData">
            <SkeletonLoader :type="viewMode" :count="perPage" />
        </div>
        <div v-else-if="filteredItems && filteredItems.length > 0">
            <!-- Grid pohled -->
            <CollectionGridView
                v-if="viewMode === 'grid'"
                :items="filteredItems"
                :bulk-mode="bulkMode"
                :selected-items="collectionsStore.selectedItems"
                @card-click="handleCardClickHandler"
                @toggle-item-selection="toggleItemSelection"
            />

            <!-- Seznam pohled -->
            <v-card v-else elevation="2" rounded="xl">
                <CollectionItemsTable :collection="collection" :items="filteredItems" :can="can" />
            </v-card>

            <!-- Stránkování -->
            <CollectionPagination
                :current-page="currentPage"
                :total-pages="totalPages"
                :per-page="perPage"
                :per-page-options="perPageOptions"
                @page-change="onPageChange"
                @per-page-change="onPerPageChange"
            />
        </div>

        <!-- Prázdné stavy -->
        <CollectionEmptyStates
            v-else
            :is-collection-empty="isCollectionEmpty"
            :has-no-filter-results="hasNoFilterResults"
            :can="can"
            @add-item="() => router.visit(route('collections.items.create', collection.id))"
            @reset-filters="resetFilters"
        />

        <!-- Dialog pro potvrzení smazání -->
        <v-dialog v-model="confirmDelete" max-width="500">
            <v-card rounded="xl">
                <v-card-title class="text-h5 d-flex align-center pa-6">
                    <v-icon class="me-2" color="error">mdi-delete-alert</v-icon>
                    {{ $t('ui.dialogs.delete') }}
                </v-card-title>
                <v-card-text class="px-6">
                    <p class="mb-2">{{ $t('collections.delete.confirmation', { name: collection ? collection.name : '' }) }}</p>
                    <v-alert 
                        v-if="collection && collection.is_default"
                        type="warning" 
                        variant="tonal" 
                        density="compact"
                        class="mb-2"
                        rounded="lg"
                    >
                        {{ $t('collections.delete.default_warning') }}
                    </v-alert>
                    <v-alert type="error" variant="tonal" density="compact" rounded="lg">
                        {{ $t('ui.messages.action_irreversible') }}
                    </v-alert>
                </v-card-text>
                <v-card-actions class="pa-6 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn color="default" variant="text" rounded="xl" @click="confirmDelete = false">
                        {{ $t('ui.buttons.cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="elevated" rounded="xl" @click="() => deleteCollection(collection)" :loading="isDeleting">
                        {{ $t('ui.buttons.delete') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Card Detail Modal -->
        <CardDetailModal 
            v-model="showCardDetail"
            :item="selectedCardItem"
            :can-edit="can?.update || false"
            :can-delete="can?.destroy || false"
            :can-duplicate="can?.create || false"
            @edit="(item) => handleCardEdit(item, collection)"
            @delete="(item) => handleCardDelete(item, collection)"
            @duplicate="(item) => handleCardDuplicate(item, collection)"
        />
    </v-container>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { useCollectionsStore } from '@/stores/collections';
import { useCollectionActions } from '@/composables/useCollectionActions';
import { useBulkSelection } from '@/composables/useBulkSelection';

// Komponenty
import CollectionHeader from '@/Components/Collections/CollectionHeader.vue';
import CollectionToolbar from '@/Components/Collections/CollectionToolbar.vue';
import CollectionGridView from '@/Components/Collections/CollectionGridView.vue';
import CollectionEmptyStates from '@/Components/Collections/CollectionEmptyStates.vue';
import CollectionPagination from '@/Components/Collections/CollectionPagination.vue';
import CollectionItemsTable from '@/Components/Collections/CollectionItemsTable.vue';
import CollectionStats from '@/Components/Collections/CollectionStats.vue';
import BulkActionsToolbar from '@/Components/Collections/BulkActionsToolbar.vue';
import CardDetailModal from '@/Components/Collections/CardDetailModal.vue';
import SkeletonLoader from '@/Components/Collections/SkeletonLoader.vue';

const props = defineProps({
    collection: Object,
    can: Object,
    items: Object,
    stats: Object,
});

// Composables
const collectionsStore = useCollectionsStore();
const {
    // Loading states
    isTogglingVisibility, isSettingDefault, isDeleting, confirmDelete,
    // Collection actions  
    setAsDefault, toggleVisibility, deleteCollection,
    // Card actions
    handleCardEdit, handleCardDelete, handleCardDuplicate,
    // Bulk actions
    handleBulkDuplicate, handleBulkExport, handleBulkEdit, handleBulkDelete,
} = useCollectionActions();

const {
    bulkMode,
    hasSelectedItems,
    selectedItemsCount,
    selectedItemsArray,
    toggleItemSelection,
    toggleBulkMode,
    selectAllItems,
    clearSelection,
    handleCardClick,
} = useBulkSelection();



// State pro card detail modal
const showCardDetail = ref(false);
const selectedCardItem = ref(null);

// Loading states
const isLoadingData = ref(false);
const isLoadingStats = ref(false);

// Stránkování
const page = usePage();
const filterOptions = page.props.filterOptions || {};
const perPageOptions = [2, 10, 30, 60, 100];
const currentPage = ref(props.items?.current_page || 1);
const totalPages = computed(() => props.items?.last_page || 1);

// Store computed properties
const viewMode = computed(() => collectionsStore.viewMode);
const filters = computed(() => collectionsStore.filters);
const perPage = computed(() => collectionsStore.perPage);
const showStats = computed(() => collectionsStore.showStats);
const hasActiveFilters = computed(() => collectionsStore.hasActiveFilters);

// Data computed properties
const filteredItems = computed(() => {
    if (props.items && Array.isArray(props.items.data)) {
        return props.items.data;
    } else if (Array.isArray(props.items)) {
        return props.items;
    } else {
        return [];
    }
});

const isCollectionEmpty = computed(() => 
    filteredItems.value.length === 0 && !hasActiveFilters.value
);

const hasNoFilterResults = computed(() => 
    filteredItems.value.length === 0 && hasActiveFilters.value
);

// Inicializace
onMounted(() => {
    collectionsStore.initializeFromProps(
        page.props.filters || {},
        props.items?.per_page
    );
    collectionsStore.setOnFiltersChangeCallback(onFiltersChange);
});

// Watchers
watch(() => props.items?.current_page, (val) => {
    if (val) currentPage.value = val;
});

watch(() => props.items?.per_page, (val) => {
    if (val) collectionsStore.setPerPage(val);
});

// Metody
function onPageChange(page) {
    const currentFilters = { ...filters.value };
    delete currentFilters.page;
    
    isLoadingData.value = true;
    // TODO: Add retry mechanism for pagination requests
    // TODO: Handle navigation failures gracefully
    router.visit(route('collections.show', props.collection.id), {
        method: 'get',
        data: { ...currentFilters, page, per_page: perPage.value },
        preserveScroll: true,
        preserveState: true,
        only: ['items', 'filters'],
        onFinish: () => {
            isLoadingData.value = false;
        }
    });
}

function onPerPageChange(newPerPage) {
    collectionsStore.setPerPage(newPerPage);
    
    const currentFilters = { ...filters.value };
    delete currentFilters.page;
    delete currentFilters.per_page;
    
    router.visit(route('collections.show', props.collection.id), {
        method: 'get',
        data: { ...currentFilters, page: 1, per_page: newPerPage },
        preserveScroll: true,
        preserveState: true,
        only: ['items', 'filters'],
    });
}

function resetFilters() {
    collectionsStore.resetFilters();
    
    router.visit(route('collections.show', props.collection.id), {
        method: 'get',
        data: { per_page: perPage.value },
        preserveScroll: true,
        preserveState: true,
        only: ['items', 'filters'],
    });
}

function onFiltersChange(newFilters) {
    collectionsStore.setFilters(newFilters);
    
    isLoadingData.value = true;
    router.visit(route('collections.show', props.collection.id), {
        method: 'get',
        data: newFilters,
        preserveScroll: true,
        preserveState: true,
        only: ['items', 'filters'],
        onFinish: () => {
            isLoadingData.value = false;
        }
    });
}

function toggleStats() {
    if (!showStats.value) {
        isLoadingStats.value = true;
        setTimeout(() => {
            isLoadingStats.value = false;
        }, 500);
    }
    collectionsStore.setShowStats(!showStats.value);
}

// Bulk selection methods
function selectAllItemsHandler() {
    const itemIds = filteredItems.value.map(item => item.id);
    selectAllItems(itemIds);
}

function handleCardClickHandler(item, event) {
    handleCardClick(item, event, openCardDetail);
}

// Card detail modal functions
function openCardDetail(item) {
    selectedCardItem.value = item;
    showCardDetail.value = true;
}
</script> 