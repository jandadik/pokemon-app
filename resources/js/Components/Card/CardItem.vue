<template>
    <v-card
        @click="navigateToDetail"
        class="card-item"
        hover
    >
        <v-skeleton-loader
            v-if="isLoading"
            type="image"
            height="100%"
        />
        <template v-else>
            <div class="card-img-container">
                <img 
                    :src="getCardImageUrl(card)" 
                    :alt="card.name"
                    class="card-img"
                    @error="handleImageError"
                >
                <!-- Tlačítko přidání do kolekce -->
                <v-btn
                    icon="mdi-folder-plus"
                    color="primary"
                    size="small"
                    class="add-to-collection-btn"
                    @click.stop="handleAddToCollectionClick"
                    :title="$t('catalog.cards.add_to_collection')"
                />
                <div class="card-number">{{ formatCardNumber(card.number) }}</div>
                <div v-if="card.set" class="card-set-name">{{ card.set.name }}</div>
            </div>

            <v-card-text class="pa-0 pt-3 d-flex justify-space-between align-center px-3 pb-3">
                <div :class="getRarityClass(card.rarity)" class="card-rarity text-caption">
                    {{ card.rarity }}
                </div>
                <div v-if="card.price_cm_avg30 !== null || card.price_cm_trend !== null" class="text-caption price-tag">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <span v-bind="props">
                                {{ formatNumberPrice(card.price_cm_avg30) }}
                                <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                            </span>
                        </template>
                        <div>
                            <div v-if="card.price_cm_updated_at">
                                Aktualizace CM: {{ formatUpdateDate(card.price_cm_updated_at) }}
                            </div>
                            <div class="d-flex flex-column mt-1">
                                <div v-if="card.price_cm_avg30 !== null" class="text-caption">
                                    CM Avg30: {{ formatPrice(card.price_cm_avg30) }}
                                </div>
                                <div v-if="card.price_cm_trend !== null" class="text-caption">
                                    CM Trend: {{ formatPrice(card.price_cm_trend) }}
                                </div>
                                <div v-if="card.price_tcg_market !== null" class="text-caption mt-1">
                                    TCG Market: {{ formatNumberPrice(card.price_tcg_market) }} USD
                                </div>
                                <div v-if="card.price_tcg_updated_at">
                                    Aktualizace TCG: {{ formatUpdateDate(card.price_tcg_updated_at) }}
                                </div>
                            </div>
                        </div>
                    </v-tooltip>
                </div>
                <div v-else class="text-caption text-medium-emphasis">
                    -
                </div>
            </v-card-text>
        </template>
    </v-card>
    
    <!-- Modal pro přidání do kolekce -->
    <AddToCollectionModal
        v-if="card"
        v-model="showAddToCollectionModal"
        :card="card"
    />
</template>

<script setup>
import { ref } from 'vue';
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';
import { router } from '@inertiajs/vue3';
import AddToCollectionModal from '@/Components/Collections/AddToCollectionModal.vue';
import { useAuthStore } from '@/stores/authStore';
// Import sdílených utilit
import {
    getCardImageUrl,
    // getTypeIcon, // Není potřeba v CardItem
    // getTypeColor, // Není potřeba v CardItem
    getRarityClass,
    formatCardNumber,
    formatNumberPrice,
    formatPrice,
    formatUpdateDate,
    handleImageError
    // getPriceValue // Není potřeba, cena se řeší přímo v šabloně
} from '@/composables/useCardUtils';

const props = defineProps({
    card: {
        type: Object,
        required: true
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});

// Auth store a reaktivní proměnné
const auth = useAuthStore();
const showAddToCollectionModal = ref(false);

// Handler pro přidání do kolekce
const handleAddToCollectionClick = () => {
    if (auth.isLoggedIn) {
        showAddToCollectionModal.value = true;
    } else {
        const redirectUrl = window.location.href;
        router.visit(`/login?redirect=${encodeURIComponent(redirectUrl)}`);
    }
};

const navigateToDetail = () => {
    // Získání aktuální URL jako referrer
    const currentUrl = window.location.pathname + window.location.search;
    
    // Návštěva detailu karty s předáním referreru
    router.visit(`/cards/${props.card.id}?referrer=${encodeURIComponent(currentUrl)}`, {
        preserveScroll: false,
        preserveState: true
    });
};
</script>

<style scoped>
.card-item {
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
    position: relative;
}

.card-item:hover {
    transform: translateY(-5px);
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
}

.card-img-container {
    position: relative;
    overflow: hidden;
    background-color: #f5f5f5;
    padding-top: 140%; /* Poměr stran 1:1.4 pro karty Pokémon */
}

.card-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.card-item:hover .card-img {
    transform: scale(1.03);
}

.card-number {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
    z-index: 1;
    line-height: 1.2;
}

.card-set-name {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 500;
    z-index: 1;
    line-height: 1.2;
    max-width: 65%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.add-to-collection-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 2;
    opacity: 1;
}

.card-rarity {
    max-width: 70%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

.card-item {
    position: relative;
    overflow: hidden;
}
</style> 