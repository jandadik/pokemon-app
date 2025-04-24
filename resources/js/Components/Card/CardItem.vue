<template>
    <v-card
        :to="`/cards/${card.id}`"
        class="h-100 card-item"
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
                <div class="card-number">{{ formatCardNumber(card.number) }}</div>
                <div v-if="card.set" class="card-set-name">{{ card.set.name }}</div>
            </div>

            <v-card-text class="pa-0 pt-3 d-flex justify-space-between align-center px-3 pb-3">
                <div :class="getRarityClass(card.rarity)" class="card-rarity text-caption">
                    {{ card.rarity }}
                </div>
                <div v-if="card.prices_cm && card.prices_cm.avg30 !== null" class="text-caption price-tag">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props }">
                            <span v-bind="props">
                                {{ formatNumberPrice(card.prices_cm.avg30) }}
                                <v-icon size="x-small" icon="mdi-currency-eur" class="ms-1" />
                            </span>
                        </template>
                        <div>
                            <div>Aktualizace dne: {{ formatUpdateDate(card.prices_cm.updated_at) }}</div>
                            <div class="d-flex flex-column mt-1">
                                <div v-if="card.prices_cm.avg30 !== null" class="text-caption">
                                    Cena (normální): {{ formatPrice(card.prices_cm.avg30) }}
                                </div>
                                <div v-if="card.prices_cm.reverse_holo_avg30 !== null" class="text-caption">
                                    Cena (reverse holo): {{ formatPrice(card.prices_cm.reverse_holo_avg30) }}
                                </div>
                                <div class="text-caption mt-1">ID: {{ card.prices_cm.card_id }}</div>
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
</template>

<script setup>
import { format } from 'date-fns';
import { cs } from 'date-fns/locale';

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

function getCardImageUrl(card) {
    // Pokud není karta definována, vrátíme placeholder
    if (!card) {
        return '/images/placeholder.jpg';
    }
    
    // Priorita 1: Lokální soubor z databáze (img_file_small)
    if (card.img_file_small) {
        // V databázi je relativní cesta "card_images\cel25\1.png" bez '/images/'
        // Převést obrácená lomítka na normální a přidat počáteční /images/
        const path = '/images/' + card.img_file_small.replace(/\\/g, '/');
        return path;
    }
    
    // Priorita 2: URL z API/externího zdroje (img_small)
    if (card.img_small) {
        return card.img_small;
    }
    
    // Fallback: Generovaná cesta podle čísla karty a setu
    if (card.set_id && card.number) {
        const localPath = `/images/card_images/${card.set_id}/${card.number}.png`;
        return localPath;
    }
    
    // Fallback: Placeholder
    return '/images/placeholder.jpg';
}

function getRarityClass(rarity) {
    if (!rarity) return 'text-grey';
    
    // Převedeme rarity na lowercase bez mezer pro jednodušší mapování
    const rarityKey = rarity.toLowerCase().replace(/\s+/g, '-');
    
    const classes = {
        'common': 'text-common',
        'uncommon': 'text-uncommon',
        'rare': 'text-rare',
        'rare-holo': 'text-rare-holo',
        'rare-ultra': 'text-rare-ultra',
        'rare-secret': 'text-rare-secret',
        'amazing-rare': 'text-rare-holo',
        'ultra-rare': 'text-rare-ultra',
        'secret-rare': 'text-rare-secret',
        'promo': 'text-uncommon',
    };
    
    return classes[rarityKey] || 'text-grey';
}

function formatCardNumber(number) {
    if (!number) return '000';
    
    // Extrahujeme číselnou část
    const numericPart = String(number).replace(/\D/g, '');
    
    // Doplníme nuly zleva
    return numericPart.padStart(3, '0');
}

function formatNumberPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    
    // Formátování ceny v EUR bez ikony (ikona je přidána v template)
    return new Intl.NumberFormat('cs-CZ', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}

function formatPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    
    // Formátování ceny pro tooltip
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}

function formatUpdateDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return format(date, 'd. MMMM yyyy', { locale: cs });
}

function handleImageError(event) {
    console.log('Chyba načítání obrázku', event.target.src);
    event.target.src = '/images/placeholder.jpg';
}
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