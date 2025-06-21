<template>
    <!-- TODO: Add Error Boundary for modal content -->
    <!-- TODO: Implement retry mechanism for modal actions (edit, delete, duplicate) -->
    <v-dialog 
        v-model="isOpen" 
        max-width="800" 
        persistent
        transition="dialog-transition"
    >
        <v-card rounded="xl" v-if="item">
            <v-toolbar
                color="transparent"
                density="comfortable"
                class="px-4"
            >
                <v-toolbar-title class="text-h6 font-weight-bold">
                    {{ item.card_name || item.card?.name }}
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    @click="closeModal"
                />
            </v-toolbar>

            <v-card-text class="pa-0">
                <v-row no-gutters>
                    <!-- Levý sloupec - obrázek karty -->
                    <v-col cols="12" md="5" class="pa-4">
                        <div class="card-image-container">
                            <PokeImage
                                :card-id="item.card?.id || item.card_id"
                                size="large"
                                :alt="item.card_name || item.card?.name"
                                :eager="true"
                                :rounded="true"
                                :cover="true"
                                class="rounded-lg"
                            />


                        </div>
                    </v-col>

                    <!-- Pravý sloupec - detailní informace -->
                    <v-col cols="12" md="7" class="pa-4">
                        <div class="card-details">
                            <!-- Základní informace -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3">{{ $t('collections.cards.basic_info') }}</h3>
                                <v-row dense>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.fields.name') }}:</strong><br>
                                        {{ item.card_name || item.card?.name }}
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.number') }}:</strong><br>
                                        {{ item.card_number || item.card?.number || 'N/A' }}
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.set') }}:</strong><br>
                                        {{ item.set_name || item.card?.set_name || item.card?.set?.name || 'N/A' }}
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.form.language') }}:</strong><br>
                                        {{ getLanguageLabel(item.language) }}
                                    </v-col>
                                    <v-col cols="12" v-if="item.variant_type_name">
                                        <strong>{{ $t('collections.cards.variant_type') }}:</strong><br>
                                        <v-chip size="small" color="info" variant="tonal">
                                            {{ item.variant_type_name }}
                                        </v-chip>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Stav a množství -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3">{{ $t('collections.cards.condition_quantity') }}</h3>
                                <v-row dense>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.table.condition') }}:</strong><br>
                                        <v-chip 
                                            size="small" 
                                            :color="getConditionColor(item.condition)"
                                        >
                                            {{ item.condition }}
                                        </v-chip>
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.table.quantity') }}:</strong><br>
                                        {{ item.quantity }}x
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Cenové informace -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3">{{ $t('collections.cards.price_info') }}</h3>
                                <v-row dense>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.purchase_price') }}:</strong><br>
                                        <span class="text-h6 text-green">
                                            {{ formatCurrency(purchasePrice) }}
                                        </span>
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.market_value') }}:</strong><br>
                                        <span class="text-h6 text-blue">
                                            {{ formatCurrency(marketValue) }}
                                        </span>
                                    </v-col>
                                </v-row>

                                <!-- Zisk/ztráta -->
                                <v-row dense class="mt-2">
                                    <v-col cols="12">
                                        <strong>{{ $t('collections.cards.profit_loss') }}:</strong><br>
                                        <span 
                                            class="text-h6"
                                            :class="getProfitLossColor(profitLoss)"
                                        >
                                            {{ formatCurrency(profitLoss) }}
                                            <small>({{ getProfitLossPercentage(purchasePrice, marketValue) }}%)</small>
                                        </span>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Metadata -->
                            <div class="mb-4">
                                <h3 class="text-subtitle-1 mb-2">{{ $t('collections.cards.metadata') }}</h3>
                                <v-row dense class="text-caption">
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.date_added') }}:</strong><br>
                                        {{ formatDate(item.created_at) }}
                                    </v-col>
                                    <v-col cols="6">
                                        <strong>{{ $t('collections.cards.last_updated') }}:</strong><br>
                                        {{ formatDate(item.updated_at) }}
                                    </v-col>
                                </v-row>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>

            <!-- Akční tlačítka -->
            <v-card-actions class="pa-4">
                <v-btn
                    v-if="canEdit"
                    color="primary"
                    variant="elevated"
                    prepend-icon="mdi-pencil"
                    @click="editItem"
                    rounded="xl"
                >
                    {{ $t('collections.buttons.edit') }}
                </v-btn>
                <v-btn
                    v-if="canDuplicate"
                    color="secondary"
                    variant="outlined"
                    prepend-icon="mdi-content-copy"
                    @click="duplicateItem"
                    rounded="xl"
                >
                    {{ $t('collections.buttons.duplicate') }}
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="canDelete"
                    color="error"
                    variant="outlined"
                    prepend-icon="mdi-delete"
                    @click="deleteItem"
                    rounded="xl"
                >
                    {{ $t('collections.buttons.delete') }}
                </v-btn>
                <v-btn
                    variant="text"
                    @click="closeModal"
                    rounded="xl"
                >
                    {{ $t('ui.buttons.close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed, watch, getCurrentInstance } from 'vue'
import PokeImage from '@/Components/Card/PokeImage.vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    item: {
        type: Object,
        default: null
    },
    canEdit: {
        type: Boolean,
        default: true
    },
    canDelete: {
        type: Boolean,
        default: true
    },
    canDuplicate: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits([
    'update:modelValue',
    'edit',
    'delete',
    'duplicate'
])

// Access global $t function for translations
const instance = getCurrentInstance()
const $t = instance.appContext.config.globalProperties.$t

const isOpen = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

// Computed properties pro lepší reactivity
const marketValue = computed(() => {
    return props.item?.market_prices?.avg30 || 0
})

const purchasePrice = computed(() => {
    return props.item?.purchase_price || 0
})

const profitLoss = computed(() => {
    return marketValue.value - purchasePrice.value
})

// Watch for item changes to update modal state
watch(() => props.item, (newItem) => {
    if (newItem) {
        // Modal is opening with new item
        console.log('CardDetailModal opened with item:', newItem.card_name || newItem.card?.name);
    }
}, { immediate: true })

function closeModal() {
    isOpen.value = false
}

function editItem() {
    emit('edit', props.item)
    closeModal()
}

function deleteItem() {
    emit('delete', props.item)
    closeModal()
}

function duplicateItem() {
    emit('duplicate', props.item)
    closeModal()
}

// Helper functions
function getConditionColor(condition) {
    const colors = {
        'mint': 'success',
        'near_mint': 'light-green',
        'excellent': 'green',
        'good': 'orange',
        'played': 'deep-orange',
        'poor': 'red'
    }
    return colors[condition] || 'grey'
}

function getRarityColor(rarity) {
    const colors = {
        'Common': 'grey',
        'Uncommon': 'green',
        'Rare': 'blue',
        'Double Rare': 'purple',
        'Illustration Rare': 'pink',
        'Special Illustration Rare': 'red',
        'Hyper Rare': 'amber',
        'Secret Rare': 'deep-purple'
    }
    return colors[rarity] || 'grey'
}

function getLanguageLabel(language) {
    const languages = {
        'en': 'English',
        'cs': 'Čeština',
        'de': 'Deutsch',
        'fr': 'Français',
        'es': 'Español',
        'it': 'Italiano',
        'ja': '日本語'
    }
    return languages[language] || language
}

function formatCurrency(value) {
    if (!value) return '€0.00'
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR'
    }).format(value)
}

function formatDate(dateString) {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('cs-CZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

function getProfitLossColor(amount) {
    if (amount > 0) return 'text-success'
    if (amount < 0) return 'text-error'
    return 'text-medium-emphasis'
}

function getProfitLossPercentage(purchase, market) {
    if (!purchase || purchase === 0) return '0'
    if (!market) market = 0
    const percentage = ((market - purchase) / purchase) * 100
    return percentage > 0 ? `+${percentage.toFixed(1)}` : percentage.toFixed(1)
}
</script>

<style scoped>
.card-image-container {
    position: relative;
    overflow: hidden;
    background-color: #f5f5f5;
    border-radius: 12px;
}

.card-img {
    width: 100%;
    height: auto;
    object-fit: cover;
    transition: transform 0.3s;
}

.card-badges {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}



.card-details {
    /* Odstraněny max-height a overflow pro eliminaci posuvníků */
    overflow: visible;
}

/* Mobile responsiveness */
@media (max-width: 600px) {
    .v-dialog {
        margin: 12px;
    }
}
</style> 