import { computed } from 'vue';
import { useCollectionsStore } from '@/stores/collections';

export function useBulkSelection() {
    const collectionsStore = useCollectionsStore();

    // Computed properties
    const bulkMode = computed(() => collectionsStore.bulkMode);
    const hasSelectedItems = computed(() => collectionsStore.hasSelectedItems);
    const selectedItemsCount = computed(() => collectionsStore.selectedItemsCount);
    const selectedItemsArray = computed(() => collectionsStore.selectedItemsArray);

    // Methods
    function toggleItemSelection(itemId) {
        collectionsStore.toggleItemSelection(itemId);
    }

    function toggleBulkMode() {
        collectionsStore.setBulkMode(!bulkMode.value);
        if (!bulkMode.value) {
            collectionsStore.clearSelectedItems();
        }
    }

    function selectAllItems(itemIds) {
        collectionsStore.selectAllItems(itemIds);
    }

    function clearSelection() {
        collectionsStore.clearSelectedItems();
    }

    function handleCardClick(item, event, openCardDetail) {
        if (bulkMode.value) {
            event.preventDefault();
            toggleItemSelection(item.id);
        } else {
            openCardDetail(item);
        }
    }

    return {
        // Computed
        bulkMode,
        hasSelectedItems,
        selectedItemsCount,
        selectedItemsArray,
        
        // Methods
        toggleItemSelection,
        toggleBulkMode,
        selectAllItems,
        clearSelection,
        handleCardClick,
    };
} 