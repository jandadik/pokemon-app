<template>
    <!-- TODO: Add Error Boundary for bulk operations -->
    <!-- TODO: Implement rollback mechanism for failed bulk operations -->
    <v-card elevation="2" rounded="xl" color="primary" variant="tonal" class="mb-3">
        <v-card-text class="pa-3">
            <v-row align="center" dense>
                <v-col cols="auto">
                    <v-icon class="me-2">mdi-checkbox-multiple-marked</v-icon>
                    <span class="font-weight-bold">{{ selectedCount }} {{ selectedCount === 1 ? 'položka vybrána' : selectedCount < 5 ? 'položky vybrány' : 'položek vybráno' }}</span>
                </v-col>
                <v-col cols="auto">
                    <v-btn
                        variant="text"
                        size="small"
                        @click="$emit('select-all')"
                        class="me-2"
                    >
                        Vybrat vše ({{ totalItems }})
                    </v-btn>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="auto">
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-pencil"
                        @click="$emit('bulk-edit')"
                        class="me-2"
                    >
                        Upravit
                    </v-btn>
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-content-copy"
                        @click="$emit('bulk-duplicate')"
                        class="me-2"
                    >
                        Duplikovat
                    </v-btn>
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-export"
                        @click="$emit('bulk-export')"
                        class="me-2"
                    >
                        Exportovat
                    </v-btn>
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-delete"
                        color="error"
                        @click="confirmDelete"
                        class="me-2"
                    >
                        Smazat
                    </v-btn>
                    <v-btn
                        variant="text"
                        size="small"
                        prepend-icon="mdi-close"
                        @click="$emit('clear-selection')"
                    >
                        Zrušit
                    </v-btn>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>

    <!-- Confirmation Dialog for Delete -->
    <v-dialog v-model="deleteConfirmDialog" max-width="500">
        <v-card rounded="xl">
            <v-card-title class="text-h6">
                <v-icon class="me-2" color="error">mdi-delete-alert</v-icon>
                {{ $t('ui.dialogs.delete') }}
            </v-card-title>
            <v-card-text>
                <p>{{ $t('ui.messages.confirm_delete') }}</p>
                <p><strong>{{ selectedCount }} {{ selectedCount === 1 ? 'položka' : selectedCount < 5 ? 'položky' : 'položek' }}</strong> bude trvale {{ selectedCount === 1 ? 'smazána' : 'smazáno' }}.</p>
                <p class="text-warning mt-2">{{ $t('ui.messages.action_irreversible') }}</p>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    @click="deleteConfirmDialog = false"
                >
                    {{ $t('ui.buttons.cancel') }}
                </v-btn>
                <v-btn
                    color="error"
                    variant="elevated"
                    @click="handleDelete"
                    :loading="isDeleting"
                >
                    {{ $t('ui.buttons.delete') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, getCurrentInstance } from 'vue'

const props = defineProps({
    selectedCount: {
        type: Number,
        required: true
    },
    totalItems: {
        type: Number,
        required: true
    },
    selectedItems: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits([
    'select-all',
    'bulk-edit',
    'bulk-duplicate', 
    'bulk-export',
    'bulk-delete',
    'clear-selection'
])

// Access global $t function for translations
const instance = getCurrentInstance()
const $t = instance.appContext.config.globalProperties.$t

// State for delete confirmation
const deleteConfirmDialog = ref(false)
const isDeleting = ref(false)

function confirmDelete() {
    deleteConfirmDialog.value = true
}

async function handleDelete() {
    isDeleting.value = true
    try {
        emit('bulk-delete', props.selectedItems)
        deleteConfirmDialog.value = false
    } catch (error) {
        console.error('Error during bulk delete:', error)
    } finally {
        isDeleting.value = false
    }
}
</script>

<style scoped>
.bulk-actions-toolbar {
    position: sticky;
    top: 20px;
    z-index: 10;
}

/* Mobile responsiveness */
@media (max-width: 600px) {
    .v-card-text {
        padding: 8px !important;
    }
    
    .v-btn {
        min-width: auto !important;
        padding: 0 8px !important;
    }
    
    .v-btn .v-btn__prepend {
        margin-inline-end: 4px !important;
    }
}

@media (max-width: 480px) {
    .v-row {
        flex-direction: column;
        gap: 8px;
    }
    
    .v-spacer {
        display: none;
    }
}
</style> 