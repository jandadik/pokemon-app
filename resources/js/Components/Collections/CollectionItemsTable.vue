<template>
    <!-- Desktop Table -->
    <v-card class="d-none d-md-block elevation-2" rounded="lg">
        <v-data-table
            :headers="headers"
            :items="items"
            :loading="loading"
            class="elevation-0" 
            item-value="id"
            density="comfortable"
            hover
            :items-per-page="-1"
            hide-default-footer
        >
            <template v-slot:[`item.card_name`]="{ item }">
                <div class="d-flex align-center py-2">
                    <div class="mr-3 card-image-container">
                        <PokeImage
                            :card-id="item.card?.id || item.card_id"
                            size="small"
                            :alt="item.card?.name || item.card_name || 'Card image'"
                            :aspect-ratio="0.714"
                            :cover="true"
                            :width="48"
                            :height="67"
                            :rounded="true"
                            class="list-card-image"
                        />
                    </div>
                    <div>
                        <div class="font-weight-medium text-body-1">{{ item.card?.name || item.card_name }}</div>
                        <div class="text-body-2 text-medium-emphasis">{{ item.variant?.name || item.variant_name }}</div>
                    </div>
                </div>
            </template>

            <template v-slot:[`item.variant_type_name`]="{ item }">
                <v-chip
                    v-if="item.variant_type_name"
                    color="info"
                    size="small"
                    variant="tonal"
                >
                    {{ item.variant_type_name }}
                </v-chip>
                <span v-else class="text-medium-emphasis">—</span>
            </template>

            <template v-slot:[`item.condition`]="{ item }">
                <v-chip
                    :color="getConditionColor(item.condition)"
                    size="small"
                    variant="tonal"
                >
                    {{ item.condition ? $t('collections.form.conditions.' + item.condition) : '' }}
                </v-chip>
            </template>

            <template v-slot:[`item.language`]="{ item }">
                <v-chip
                    color="primary"
                    size="small"
                    variant="outlined"
                >
                    {{ item.language ? $t('collections.form.languages.' + item.language) : '' }}
                </v-chip>
            </template>

            <template v-slot:[`item.quantity`]="{ item }">
                <div class="text-center font-weight-medium">{{ item.quantity }}</div>
            </template>

            <template v-slot:[`item.purchase_price`]="{ item }">
                <div class="text-end font-weight-medium">
                    {{ formatCurrency(item.purchase_price) }}
                </div>
            </template>

            <template v-slot:[`item.market_price`]="{ item }">
                <div class="text-end">
                    <div class="font-weight-medium text-success">
                        {{ formatCurrency(item.market_prices?.avg30) }}
                    </div>
                    <div v-if="item.market_prices?.updated_at" class="text-caption text-medium-emphasis">
                        CM {{ formatDate(item.market_prices.updated_at) }}
                    </div>
                </div>
            </template>

            <template v-slot:[`item.actions`]="{ item }">
                <div class="d-flex justify-end gap-1">
                    <v-tooltip v-if="can?.update" :text="$t('collections.buttons.edit')" location="top">
                        <template #activator="{ props: tooltipProps }">
                            <v-btn 
                                v-bind="tooltipProps"
                                icon="mdi-pencil" 
                                size="small" 
                                variant="text" 
                                color="primary"
                                density="comfortable"
                                @click="editItem(item)"
                            />
                        </template>
                    </v-tooltip>
                    <v-tooltip v-if="can?.update" :text="$t('collections.buttons.delete')" location="top">
                        <template #activator="{ props: tooltipProps }">
                            <v-btn 
                                v-bind="tooltipProps"
                                icon="mdi-delete" 
                                size="small" 
                                variant="text" 
                                color="error"
                                density="comfortable"
                                @click="openDeleteDialog(item)"
                            />
                        </template>
                    </v-tooltip>
                </div>
            </template>

            <template v-slot:no-data>
                <div class="text-center py-8">
                    <v-icon size="64" color="primary" class="mb-4">mdi-cards-outline</v-icon>
                    <h3 class="text-h6 mb-2">{{ $t('collections.items.empty_title') }}</h3>
                    <p class="text-body-2 text-medium-emphasis mb-4">{{ $t('collections.items.empty_text') }}</p>
                    <v-btn
                        v-if="can?.update"
                        color="primary"
                        variant="elevated"
                        prepend-icon="mdi-plus"
                        @click="$emit('add-item')"
                    >
                        {{ $t('collections.items.add_new_item') }}
                    </v-btn>
                </div>
            </template>
            <template v-slot:bottom></template>
        </v-data-table>
    </v-card>

    <!-- Mobile Cards -->
    <div class="d-md-none">
        <!-- Empty state for mobile -->
        <v-card v-if="!items || items.length === 0" class="text-center py-8" variant="tonal" rounded="lg">
            <v-card-text>
                <v-icon size="64" color="primary" class="mb-4">mdi-cards-outline</v-icon>
                <h3 class="text-h6 mb-2">{{ $t('collections.items.empty_title') }}</h3>
                <p class="text-body-2 text-medium-emphasis mb-4">{{ $t('collections.items.empty_text') }}</p>
                <v-btn
                    v-if="can?.update"
                    color="primary"
                    variant="elevated"
                    prepend-icon="mdi-plus"
                    block
                    size="large"
                    @click="$emit('add-item')"
                >
                    {{ $t('collections.items.add_new_item') }}
                </v-btn>
            </v-card-text>
        </v-card>

        <!-- Mobile item cards -->
        <div v-else class="mobile-items-grid">
            <v-card
                v-for="item in items"
                :key="item.id"
                class="mb-3 item-card"
                elevation="2"
                rounded="lg"
            >
                <v-card-text class="pb-2">
                    <!-- Card image and basic info -->
                    <div class="d-flex align-start mb-3">
                        <div class="mr-3 card-image-container">
                            <PokeImage
                                :card-id="item.card?.id || item.card_id"
                                size="small"
                                :alt="item.card?.name || item.card_name || 'Card image'"
                                :aspect-ratio="0.714"
                                :cover="true"
                                :width="56"
                                :height="78"
                                :rounded="true"
                                class="mobile-card-image"
                            />
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="text-h6 mb-1 card-name">{{ item.card?.name || item.card_name }}</h4>
                            <p class="text-body-2 text-medium-emphasis mb-1">{{ item.variant?.name || item.variant_name }}</p>
                            <p v-if="item.variant_type_name" class="text-caption text-medium-emphasis mb-2">{{ item.variant_type_name }}</p>
                            
                            <!-- Quantity badge -->
                            <v-chip
                                color="primary"
                                size="small"
                                variant="elevated"
                                prepend-icon="mdi-numeric"
                            >
                                {{ item.quantity }}x
                            </v-chip>
                        </div>
                    </div>

                    <!-- Details row -->
                    <div class="d-flex align-center justify-space-between mb-2">
                        <div class="d-flex flex-column gap-1">
                            <v-chip
                                :color="getConditionColor(item.condition)"
                                size="small"
                                variant="tonal"
                            >
                                {{ item.condition ? $t('collections.form.conditions.' + item.condition) : '' }}
                            </v-chip>
                            <v-chip
                                color="surface-variant"
                                size="small"
                                variant="outlined"
                            >
                                {{ item.language ? $t('collections.form.languages.' + item.language) : '' }}
                            </v-chip>
                        </div>
                        <div class="text-end">
                            <div class="text-h6 font-weight-bold text-success">
                                {{ formatCurrency(item.market_prices?.avg30) }}
                            </div>
                            <div v-if="item.purchase_price" class="text-caption text-medium-emphasis">
                                Pořizovací: {{ formatCurrency(item.purchase_price) }}
                            </div>
                        </div>
                    </div>
                </v-card-text>

                <v-card-actions class="pt-0">
                    <v-spacer />
                    <v-btn 
                        v-if="can?.update"
                        variant="text"
                        color="primary"
                        size="small"
                        prepend-icon="mdi-pencil"
                        @click="editItem(item)"
                    >
                        {{ $t('collections.buttons.edit') }}
                    </v-btn>
                    <v-btn 
                        v-if="can?.update"
                        icon="mdi-delete" 
                        size="small" 
                        variant="text"
                        color="error"
                        @click="openDeleteDialog(item)"
                    />
                </v-card-actions>
            </v-card>
        </div>
    </div>

    <!-- Enhanced Delete Dialog -->
    <v-dialog v-model="deleteDialog.show" max-width="400" :fullscreen="isMobile">
        <v-card rounded="lg">
            <v-card-title class="text-h6 d-flex align-center">
                <v-icon class="me-2" color="error">mdi-delete-alert</v-icon>
                {{ $t('ui.dialogs.delete') }}
            </v-card-title>
            <v-card-text>
                <p class="text-body-1 mb-2">{{ $t('ui.messages.confirm_delete') }}</p>
                <p class="text-body-2 font-weight-medium">{{ deleteDialog.item?.card?.name || deleteDialog.item?.card_name }}</p>
                <v-alert type="warning" variant="tonal" density="compact" class="mt-3">
                    {{ $t('ui.messages.action_irreversible') }}
                </v-alert>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn 
                    color="default" 
                    variant="text" 
                    @click="closeDeleteDialog"
                    :block="isMobile"
                >
                    {{ $t('ui.buttons.cancel') }}
                </v-btn>
                <v-btn 
                    color="error" 
                    variant="elevated" 
                    @click="confirmDeleteItem" 
                    :loading="deleteDialog.loading"
                    :block="isMobile"
                >
                    {{ $t('ui.buttons.delete') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed, getCurrentInstance } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDisplay } from 'vuetify';
import PokeImage from '@/Components/Card/PokeImage.vue';

const { mobile: isMobile } = useDisplay();
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

const props = defineProps({
    items: {
        type: Array,
        required: true,
        default: () => []
    },
    collection: Object,
    can: Object,
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['add-item']);

const headers = computed(() => [
    { title: $t('collections.fields.name'), key: 'card_name', sortable: true, minWidth: '280px' },
    { title: $t('collections.cards.variant_type'), key: 'variant_type_name', sortable: true, align: 'center', width: '150px' },
    { title: $t('collections.cards.table.condition'), key: 'condition', sortable: true, align: 'center' },
    { title: $t('collections.form.language'), key: 'language', sortable: true, align: 'center' },
    { title: $t('collections.cards.table.quantity'), key: 'quantity', sortable: true, align: 'center', width: '100px' },
    { title: $t('collections.form.purchase_price'), key: 'purchase_price', sortable: true, align: 'end', width: '120px' },
    { title: $t('collections.cards.table.market_price'), key: 'market_price', sortable: false, align: 'end', width: '120px' },
    { title: $t('collections.fields.actions'), key: 'actions', sortable: false, align: 'end', width: '120px' },
]);

const deleteDialog = ref({
    show: false,
    item: null,
    loading: false,
});

// Utility function for condition colors
const getConditionColor = (condition) => {
    const conditionColors = {
        'near_mint': 'success',
        'excellent': 'primary', 
        'good': 'warning',
        'played': 'orange',
        'poor': 'error'
    };
    return conditionColors[condition] || 'default';
};

// Utility function for currency formatting
const formatCurrency = (value) => {
    if (!value || value === 0) return '—';
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

// Utility function for date formatting
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('cs-CZ', { 
        day: '2-digit', 
        month: '2-digit' 
    });
};

const openDeleteDialog = (item) => {
    deleteDialog.value.item = item;
    deleteDialog.value.show = true;
};

const closeDeleteDialog = () => {
    deleteDialog.value.show = false;
    deleteDialog.value.item = null;
    deleteDialog.value.loading = false;
};

const confirmDeleteItem = () => {
    if (deleteDialog.value.item) {
        deleteDialog.value.loading = true;
        router.delete(route('collections.items.destroy', { 
            collection: props.collection.id, 
            item: deleteDialog.value.item.id 
        }), {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
            },
            onError: (errors) => {
                console.error('Error deleting item:', errors);
                closeDeleteDialog();
            },
            onFinish: () => {
                deleteDialog.value.loading = false;
            }
        });
    }
};

const editItem = (item) => {
    router.visit(route('collections.items.edit', { 
        collection: props.collection.id, 
        item: item.id 
    }));
};
</script>

<style scoped>
.item-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.item-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.12) !important;
}

.card-image-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.list-card-image {
    border-radius: 8px;
}

.mobile-card-image {
    border-radius: 8px;
}

.card-name {
    line-height: 1.2;
}

.mobile-items-grid {
    gap: 12px;
}

.v-data-table :deep(tbody tr:hover) {
    background-color: rgba(var(--v-theme-on-surface), 0.04) !important;
}

.v-data-table :deep(th) {
    font-size: 0.875rem !important;
    font-weight: 600 !important;
}

.v-data-table :deep(td) {
    padding: 12px 16px !important;
}

.gap-1 {
    gap: 4px;
}
</style> 