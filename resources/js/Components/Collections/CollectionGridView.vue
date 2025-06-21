<template>
    <!-- Grid pohled -->
    <!-- TODO: Add Error Boundary around the entire grid view -->
    <!-- TODO: Implement fallback UI for when grid components fail -->
    <div class="card-grid">
        <v-card 
            v-for="item in items" 
            :key="item.id"
            class="card-item elevation-2"
            :class="{ 'card-selected': bulkMode && isItemSelected(item.id), 'card-bulk-mode': bulkMode }"
            rounded="xl"
            hover
            @click="handleCardClick(item, $event)"
        >
            <div class="card-image-container">
                <PokeImage
                    :card-id="item.card.id"
                    size="small"
                    :alt="item.card.name"
                    :aspect-ratio="0.714"
                    :cover="true"
                    :queue-load="true"
                    class="card-image"
                />
                
                <!-- Checkbox pro bulk selection -->
                <v-checkbox
                    v-if="bulkMode"
                    :model-value="isItemSelected(item.id)"
                    @update:model-value="toggleItemSelection(item.id)"
                    @click.stop
                    color="primary"
                    hide-details
                    class="selection-checkbox"
                />
            </div>
            
            <v-card-text class="pa-3">
                <div class="text-body-2 font-weight-bold text-truncate mb-1">
                    {{ item.card.name }}
                </div>
                <div class="text-caption text-medium-emphasis text-truncate mb-1">
                    {{ item.card.set_name }} • #{{ item.card.number }}
                </div>
                <div v-if="item.variant_type_name || item.quantity > 1" class="text-caption text-medium-emphasis text-truncate mb-2 d-flex justify-space-between align-center">
                    <span v-if="item.variant_type_name">{{ item.variant_type_name }}</span>
                    <span v-else>&nbsp;</span>
                    <span v-if="item.quantity > 1" class="font-weight-bold">{{ item.quantity }}x</span>
                </div>
                <div class="d-flex justify-space-between align-center">
                    <v-chip
                        v-if="item.card.rarity"
                        size="x-small"
                        :color="getRarityColor(item.card.rarity)"
                        variant="tonal"
                    >
                        {{ item.card.rarity }}
                    </v-chip>
                    <div class="d-flex flex-column align-end">
                        <div class="text-caption font-weight-bold text-success">
                            {{ formatCurrency(item.market_prices?.avg30 || 0) }}
                        </div>
                        <div v-if="item.purchase_price" class="text-caption text-medium-emphasis">
                            {{ formatCurrency(item.purchase_price) }}
                        </div>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup>
import PokeImage from '@/Components/Card/PokeImage.vue';

const props = defineProps({
    items: Array,
    bulkMode: Boolean,
    selectedItems: Set,
});

const emit = defineEmits([
    'card-click',
    'toggle-item-selection'
]);

// Helper funkce pro styling
const getRarityColor = (rarity) => {
    const colors = {
        'Common': 'grey',
        'Uncommon': 'green',
        'Rare': 'blue',
        'Double Rare': 'purple',
        'Illustration Rare': 'pink',
        'Special Illustration Rare': 'red',
        'Hyper Rare': 'amber',
        'Secret Rare': 'deep-purple'
    };
    return colors[rarity] || 'grey';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

function handleCardClick(item, event) {
    emit('card-click', item, event);
}

function toggleItemSelection(itemId) {
    emit('toggle-item-selection', itemId);
}

function isItemSelected(itemId) {
    // Tuto logiku předáme jako prop z rodiče
    return props.selectedItems?.has(itemId) || false;
}
</script>

<style scoped>
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.card-item {
    position: relative;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
}

.card-image-container {
    position: relative;
    overflow: hidden;
}

.card-image {
    border-radius: 12px 12px 0 0;
}

.selection-checkbox {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    backdrop-filter: blur(4px);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-bulk-mode {
    cursor: pointer;
}

.card-selected {
    border: 2px solid #1976d2;
    transform: scale(0.98);
}

.card-selected:hover {
    transform: scale(0.98) !important;
}

@media (max-width: 600px) {
    .card-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
}
</style> 