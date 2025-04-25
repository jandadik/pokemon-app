import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

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
    const currentPage = ref(1);

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
            view: view_mode.value,
            page: currentPage.value
        };
    });

    const sortOption = computed({
        get: () => {
            return `${sort_by.value}_${sort_direction.value}`;
        },
        set: (val) => {
            console.log('cardStore: sortOption setter received:', val);
            if (!val || typeof val !== 'string') {
                 console.error("cardStore: Neplatná hodnota pro řazení:", val);
                 // Možná nastavit default?
                 sort_by.value = 'name';
                 sort_direction.value = 'asc';
                 return;
             }
             
             const parts = val.split('_');
             if (parts.length < 2) {
                 console.error("cardStore: Neplatný formát pro řazení:", val);
                 // Možná nastavit default?
                 sort_by.value = 'name';
                 sort_direction.value = 'asc';
                 return;
             }

             // Směr je poslední část
             const potentialDirection = parts[parts.length - 1];
             
             if (potentialDirection === 'asc' || potentialDirection === 'desc') {
                 sort_direction.value = potentialDirection;
                 // Klíč je vše před posledním podtržítkem
                 sort_by.value = parts.slice(0, -1).join('_'); 
             } else {
                 // Pokud poslední část není 'asc' ani 'desc', je něco špatně
                 console.warn(`cardStore: Neplatný směr řazení v hodnotě: ${val}. Používám výchozí řazení.`);
                 sort_by.value = 'name'; 
                 sort_direction.value = 'asc';
             }
            console.log(`cardStore: Parsed - sort_by: ${sort_by.value}, sort_direction: ${sort_direction.value}`);
        }
    });

    // Akce
    function setSearch(value) {
        search.value = value;
        debouncedApplyFilters();
    }

    function setType(value) {
        type.value = value;
        applyFilters(true);
    }

    function setRarity(value) {
        rarity.value = value;
        applyFilters(true);
    }

    function setSetId(value) {
        set_id.value = value;
        applyFilters(true);
    }

    function setSortOption(value) {
        sortOption.value = value;
        applyFilters(true);
    }

    function setPerPage(value) {
        per_page.value = value;
        applyFilters(true);
    }

    function setViewMode(value) {
        view_mode.value = value;
        // Uložíme preferenci do localStorage
        localStorage.setItem('preferredCardView', value);
        applyFilters(false);
    }

    function setTotalCards(value) {
        total_cards.value = value;
    }

    function setLoading(value) {
        isLoading.value = value;
    }

    function setPage(value) {
        currentPage.value = value;
        applyFilters(false);
    }

    function resetFilters() {
        search.value = '';
        type.value = '';
        rarity.value = '';
        set_id.value = '';
        sort_by.value = 'name';
        sort_direction.value = 'asc';
        per_page.value = 30;
        currentPage.value = 1;
        applyFilters(true);
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
        
        if (props.cards) {
            if (props.cards.total) {
                total_cards.value = props.cards.total;
            }
            
            if (props.cards.current_page) {
                currentPage.value = props.cards.current_page;
            }
        }

        // Inicializace event listenerů pro sledování stavu načítání
        router.on('start', () => {
            isLoading.value = true;
        });
        
        router.on('finish', () => {
            isLoading.value = false;
        });
    }

    // Metoda pro aplikaci filtrů a přechod na novou stránku
    function applyFilters(resetPage = true) {
        if (resetPage) {
            currentPage.value = 1;
        }

        console.log('cardStore: Applying filters with request data:', filters.value);
        router.get('/cards', filters.value, {
            preserveState: true,
            preserveScroll: true,
            only: ['cards']
        });
    }

    // Debounced verze pro vyhledávání
    const debouncedApplyFilters = debounce(() => {
        applyFilters(true);
    }, 500);

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
        currentPage,
        
        // Počítané vlastnosti
        hasActiveFilters,
        filters,
        sortOption,
        
        // Akce
        setSearch,
        setType,
        setRarity,
        setSetId,
        setSortOption,
        setPerPage,
        setViewMode,
        setTotalCards,
        setLoading,
        setPage,
        resetFilters,
        applyFilters,
        
        // Inicializace
        initializeFromLocalStorage,
        initializeFromProps
    };
}); 