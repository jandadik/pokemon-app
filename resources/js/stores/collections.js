import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

// Debounce helper
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

export const useCollectionsStore = defineStore('collections', () => {
  // State
  const viewMode = ref('grid')
  const filters = ref({})
  const perPage = ref(30)
  const showStats = ref(false)
  const selectedItems = ref(new Set())
  const bulkMode = ref(false)
  
  // Callback pro notifikaci změn (nastavuje se z komponenty)
  const onFiltersChangeCallback = ref(null)
  
  // Getters
  const hasActiveFilters = computed(() => {
    return Object.entries(filters.value).some(([key, value]) => {
      if (key === 'page' || key === 'per_page') return false
      return value !== null && value !== undefined && value !== ''
    })
  })
  
  const hasSelectedItems = computed(() => {
    return selectedItems.value.size > 0
  })
  
  const selectedItemsCount = computed(() => {
    return selectedItems.value.size
  })
  
  const selectedItemsArray = computed(() => {
    return Array.from(selectedItems.value)
  })
  
  // Actions
  function setViewMode(mode) {
    viewMode.value = mode
    saveToLocalStorage()
  }
  
  function setFilters(newFilters) {
    filters.value = { ...newFilters }
    saveToLocalStorage()
  }
  
  function updateFilter(key, value) {
    if (value === null || value === undefined || value === '') {
      delete filters.value[key]
    } else {
      filters.value[key] = value
    }
    saveToLocalStorage()
    
    // Notifikace změny s debounce pro search
    if (key === 'search') {
      debouncedNotifyChange()
    } else {
      notifyChange()
    }
  }
  
  // Debounced notifikace pro search
  const debouncedNotifyChange = debounce(() => {
    if (onFiltersChangeCallback.value) {
      onFiltersChangeCallback.value({ ...filters.value })
    }
  }, 500)
  
  // Okamžitá notifikace pro ostatní filtry
  function notifyChange() {
    if (onFiltersChangeCallback.value) {
      onFiltersChangeCallback.value({ ...filters.value })
    }
  }
  
  function setOnFiltersChangeCallback(callback) {
    onFiltersChangeCallback.value = callback
  }
  
  function resetFilters() {
    filters.value = {}
    saveToLocalStorage()
  }
  
  function setPerPage(value) {
    perPage.value = value
    saveToLocalStorage()
  }

  function setShowStats(value) {
    showStats.value = value
    saveToLocalStorage()
  }

  function setBulkMode(value) {
    bulkMode.value = value
    if (!value) {
      clearSelectedItems()
    }
  }

  function toggleItemSelection(itemId) {
    if (selectedItems.value.has(itemId)) {
      selectedItems.value.delete(itemId)
    } else {
      selectedItems.value.add(itemId)
    }
    
    // Auto-enable bulk mode when first item is selected
    if (selectedItems.value.size > 0 && !bulkMode.value) {
      bulkMode.value = true
    }
    
    // Auto-disable bulk mode when no items are selected
    if (selectedItems.value.size === 0 && bulkMode.value) {
      bulkMode.value = false
    }
  }

  function selectAllItems(itemIds) {
    selectedItems.value.clear()
    itemIds.forEach(id => selectedItems.value.add(id))
    if (itemIds.length > 0) {
      bulkMode.value = true
    }
  }

  function clearSelectedItems() {
    selectedItems.value.clear()
    bulkMode.value = false
  }

  function isItemSelected(itemId) {
    return selectedItems.value.has(itemId)
  }
  
  function saveToLocalStorage() {
    const state = {
      viewMode: viewMode.value,
      filters: filters.value,
      perPage: perPage.value,
      showStats: showStats.value
    }
    localStorage.setItem('collections-view-state', JSON.stringify(state))
  }
  
  function loadFromLocalStorage() {
    try {
      const saved = localStorage.getItem('collections-view-state')
      if (saved) {
        const state = JSON.parse(saved)
        viewMode.value = state.viewMode || 'grid'
        filters.value = state.filters || {}
        perPage.value = state.perPage || 30
        showStats.value = state.showStats || false
      }
    } catch (error) {
      console.warn('Chyba při načítání stavu z localStorage:', error)
    }
  }
  
  function initializeFromProps(propsFilters, propsPerPage) {
    // Načteme z localStorage
    loadFromLocalStorage()
    
    // Pokud máme props z backendu, použijeme je (mají přednost)
    if (propsFilters && Object.keys(propsFilters).length > 0) {
      filters.value = { ...propsFilters }
    }
    
    if (propsPerPage) {
      perPage.value = propsPerPage
    }
  }
  
  return {
    // State
    viewMode,
    filters,
    perPage,
    showStats,
    selectedItems,
    bulkMode,
    
    // Getters
    hasActiveFilters,
    hasSelectedItems,
    selectedItemsCount,
    selectedItemsArray,
    
    // Actions
    setViewMode,
    setFilters,
    updateFilter,
    resetFilters,
    setPerPage,
    setShowStats,
    setBulkMode,
    toggleItemSelection,
    selectAllItems,
    clearSelectedItems,
    isItemSelected,
    initializeFromProps,
    loadFromLocalStorage,
    setOnFiltersChangeCallback
  }
}) 