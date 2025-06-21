<template>
    <div class="card-search-container">
        <v-text-field
            ref="searchInput"
            v-model="searchQuery"
            placeholder="Hledat kartu (název, kód, číslo)..."
            @focus="showResults = true; updateDropdownPosition()"
            variant="outlined"
        />
        <v-card
            v-if="showResults && (isLoading || results.length > 0 || (searchQuery.length >= 2 && !isLoading && !results.length))"
            class="search-results"
            :style="dropdownStyle"
            elevation="8"
        >
            <div v-if="isLoading" class="pa-4 text-body-2 text-medium-emphasis">
                Načítám...
            </div>
            <v-list v-else-if="results.length > 0" density="compact">
                <v-list-item
                    v-for="card in results"
                    :key="card.card_id"
                    @click="selectCard(card)"
                    class="card-item"
                >
                    <template v-slot:prepend>
                        <v-img
                            v-if="getCardImageUrl(card)"
                            :src="getCardImageUrl(card)"
                            :alt="card.name"
                            class="card-image"
                            cover
                        />
                        <div v-else class="card-placeholder">
                            N/A
                        </div>
                    </template>
                    <v-list-item-title class="text-body-2">{{ card.name }}</v-list-item-title>
                    <v-list-item-subtitle class="text-caption">
                        {{ card.set_name }} - {{ card.number }}
                    </v-list-item-subtitle>
                </v-list-item>
            </v-list>
            <div v-else-if="searchQuery.length >= 2 && !isLoading && !results.length" class="pa-4 text-body-2 text-medium-emphasis">
                Nebyly nalezeny žádné karty.
            </div>
        </v-card>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import debounce from 'lodash/debounce';
import { getCardImageUrl } from '@/composables/useCardUtils';

const emit = defineEmits(['card-selected']);

const searchQuery = ref('');
const results = ref([]);
const isLoading = ref(false);
const showResults = ref(false);
const limit = 10;
const searchInput = ref(null);

const dropdownStyle = ref({});

const updateDropdownPosition = () => {
    if (searchInput.value && searchInput.value.$el) {
        const rect = searchInput.value.$el.getBoundingClientRect();
        dropdownStyle.value = {
            top: `${rect.bottom + 12}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`
        };
    }
};

const fetchResults = async () => {
    if (searchQuery.value.length < 2) {
        results.value = [];
        showResults.value = false;
        return;
    }
    isLoading.value = true;
    showResults.value = true;
    try {
        const response = await axios.get(route('catalog.cards.lookup'), {
            params: {
                query: searchQuery.value,
                limit: limit
            }
        });
        results.value = response.data;
    } catch (error) {
        console.error('Chyba při vyhledávání karet:', error);
        results.value = []; // V případě chyby vyčistit výsledky
    }
    isLoading.value = false;
};

const debouncedFetchResults = debounce(fetchResults, 300);

watch(searchQuery, () => {
    if (searchQuery.value.length === 0) {
        results.value = [];
        showResults.value = false; // Skrýt, když je pole prázdné
    } else if (searchQuery.value.length >= 2) {
        debouncedFetchResults();
    } else {
        results.value = []; // Vyčistit výsledky, pokud je dotaz příliš krátký, ale nezavírat dropdown hned
        showResults.value = true; // Nechat otevřené, aby se zobrazil "Nebyly nalezeny žádné karty" nebo načítání
    }
});

const selectCard = (card) => {
    emit('card-selected', card);
    searchQuery.value = card.name; // Volitelně vyplnit pole jménem vybrané karty
    showResults.value = false;
    results.value = []; // Vyčistit po výběru
};

// Zavřít dropdown při kliknutí mimo komponentu
// Jednoduchá verze, pro produkci by se mohl použít robustnější způsob
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) { // Zkontroluje, zda klik nebyl uvnitř této komponenty
        showResults.value = false;
    }
};

// Přidat a odebrat listener při montáži a demontáži komponenty
import { onMounted, onUnmounted } from 'vue';
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('resize', updateDropdownPosition);
});
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', updateDropdownPosition);
});

</script>

<style scoped>
.card-search-container {
    position: relative;
    overflow: visible;
}

.search-results {
    position: fixed;
    z-index: 9999;
    margin-top: 12px;
    max-height: 384px;
    overflow-y: auto;
}

.card-item {
    cursor: pointer;
}

.card-image {
    width: 30px;
    height: 30px;
    border-radius: 4px;
    flex-shrink: 0;
}

.card-placeholder {
    width: 30px;
    height: 30px;
    background-color: #e0e0e0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #757575;
    flex-shrink: 0;
}
</style> 