import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCardStore = defineStore('cards', () => {
    // Stav
    const search = ref('');
    const type = ref('');
    const rarity = ref('');
    const set_id = ref('');
    const sort_by = ref('name');
    const sort_direction = ref('asc');
    const per_page = ref(30);
    const view_mode = ref('grid');
    const total_cards = ref(0);
    const isLoading = ref(false);

    // Počítané vlastnosti
    const hasActiveFilters = computed(() => {
        return search.value || type.value || rarity.value || set_id.value;
    });

    const filters = computed(() => {
        return {
            search: search.value,
            type: type.value,
            rarity: rarity.value,
            set_id: set_id.value,
            sort_by: sort_by.value,
            sort_direction: sort_direction.value,
            per_page: per_page.value,
            view: view_mode.value
        };
    });

    // Akce
    function setSearch(value) {
        search.value = value;
    }

    function setType(value) {
        type.value = value;
    }

    function setRarity(value) {
        rarity.value = value;
    }

    function setSetId(value) {
        set_id.value = value;
    }

    function setSortBy(value) {
        sort_by.value = value;
    }

    function setSortDirection(value) {
        sort_direction.value = value;
    }

    function setPerPage(value) {
        per_page.value = value;
    }

    function setViewMode(value) {
        view_mode.value = value;
        // Uložíme preferenci do localStorage
        localStorage.setItem('preferredCardView', value);
    }

    function setTotalCards(value) {
        total_cards.value = value;
    }

    function setLoading(value) {
        isLoading.value = value;
    }

    function resetFilters() {
        search.value = '';
        type.value = '';
        rarity.value = '';
        set_id.value = '';
        sort_by.value = 'name';
        sort_direction.value = 'asc';
        per_page.value = 30;
    }

    // Inicializace z localStorage
    function initializeFromLocalStorage() {
        const savedView = localStorage.getItem('preferredCardView');
        if (savedView) {
            view_mode.value = savedView;
        }
    }

    // Inicializace z props
    function initializeFromProps(props) {
        if (props.filters) {
            search.value = props.filters.search || '';
            type.value = props.filters.type || '';
            rarity.value = props.filters.rarity || '';
            set_id.value = props.filters.set_id || '';
            sort_by.value = props.filters.sort_by || 'name';
            sort_direction.value = props.filters.sort_direction || 'asc';
            per_page.value = props.filters.per_page || 30;
            view_mode.value = props.filters.view || 'grid';
        }
        
        if (props.cards?.total) {
            total_cards.value = props.cards.total;
        }
    }

    return {
        // Stav
        search,
        type,
        rarity,
        set_id,
        sort_by,
        sort_direction,
        per_page,
        view_mode,
        total_cards,
        isLoading,
        
        // Počítané vlastnosti
        hasActiveFilters,
        filters,
        
        // Akce
        setSearch,
        setType,
        setRarity,
        setSetId,
        setSortBy,
        setSortDirection,
        setPerPage,
        setViewMode,
        setTotalCards,
        setLoading,
        resetFilters,
        initializeFromLocalStorage,
        initializeFromProps
    };
}); 