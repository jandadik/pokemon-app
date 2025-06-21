<template>
    <v-container>
        <Head :title="$t('collections.title')" />

        <!-- Kompaktní hlavička -->
        <div class="mb-4">
            <v-row align="center" dense no-gutters>
                <v-col cols="auto" class="me-2">
                    <v-avatar size="24" color="primary" class="elevation-1">
                        <v-icon size="14" color="white">mdi-folder-multiple-outline</v-icon>
                    </v-avatar>
                </v-col>
                <v-col cols="auto" class="me-3">
                    <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
                        {{ $t('collections.title') }}
                    </h1>
                </v-col>
                <v-col cols="auto" class="me-2" v-if="props.collections && props.collections.total !== undefined">
                    <v-chip 
                        color="primary" 
                        size="x-small"
                        variant="tonal"
                    >
                        {{ props.collections.total }} {{ $t('collections.collections_count_text', props.collections.total) }}
                    </v-chip>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="auto">
                    <v-btn
                        v-if="auth.can('collections.create') || props.can?.create_collection"
                        @click="router.get(route('collections.create'))"
                        icon="mdi-plus"
                        color="primary"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon>mdi-plus</v-icon>
                        <v-tooltip activator="parent" location="bottom">
                            {{ $t('collections.buttons.create') }}
                        </v-tooltip>
                    </v-btn>
                </v-col>
            </v-row>
        </div>

        <!-- Empty state -->
        <v-card v-if="!props.collections || !props.collections.data || props.collections.data.length === 0" 
               class="text-center py-8" variant="tonal">
            <v-card-text>
                <v-icon size="64" color="primary" class="mb-4">mdi-folder-plus-outline</v-icon>
                <h3 class="text-h6 mb-2">{{ $t('collections.empty.title') }}</h3>
                <p class="text-body-2 text-medium-emphasis mb-4">{{ $t('collections.empty.text') }}</p>
                <v-btn
                    v-if="auth.can('collections.create') || props.can?.create_collection"
                    @click="router.get(route('collections.create'))"
                    color="primary"
                    variant="elevated"
                    prepend-icon="mdi-plus"
                >
                    {{ $t('collections.buttons.create_first') }}
                </v-btn>
            </v-card-text>
        </v-card>



        <!-- Collections List -->
        <div v-else>
            <!-- Desktop Table -->
            <v-card class="d-none d-md-block elevation-2 mb-4" rounded="lg">
                <v-table hover>
                    <thead>
                        <tr>
                            <th class="text-left font-weight-bold">{{ $t('collections.fields.name') }}</th>
                            <th class="text-left font-weight-bold">{{ $t('collections.fields.description') }}</th>
                            <th class="text-center font-weight-bold">{{ $t('collections.fields.is_default') }}</th>
                            <th class="text-center font-weight-bold">{{ $t('collections.fields.visibility') }}</th>
                            <th class="text-right font-weight-bold">{{ $t('collections.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="collection in props.collections.data" :key="collection.id" 
                            class="cursor-pointer hover-row"
                            @click="router.visit(route('collections.show', collection.id))">
                            <td class="py-3">
                                <div class="font-weight-medium">{{ collection.name }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-body-2">
                                    {{ collection.description ? collection.description.substring(0, 60) + (collection.description.length > 60 ? '...' : '') : '-' }}
                                </div>
                            </td>
                            <td class="text-center py-3">
                                <v-btn
                                    :icon="collection.is_default ? 'mdi-star' : 'mdi-star-outline'"
                                    :color="collection.is_default ? 'primary' : 'grey'"
                                    variant="text"
                                    size="small"
                                    @click.stop="toggleDefault(collection)"
                                    :loading="collection.updating_default" 
                                    :disabled="collection.updating_default || !props.can.setDefault"
                                    :title="collection.is_default ? $t('collections.buttons.unset_default') : $t('collections.buttons.set_default')"
                                />
                            </td>
                            <td class="text-center py-3">
                                <v-chip
                                    :color="collection.is_public ? 'success' : 'warning'"
                                    size="small"
                                    variant="tonal"
                                    @click.stop="toggleVisibility(collection)"
                                    :loading="collection.updating_visibility"
                                    :disabled="collection.updating_visibility || !props.can.update"
                                    class="cursor-pointer"
                                >
                                    {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
                                </v-chip>
                            </td>
                            <td class="text-right py-3">
                                <div class="d-flex justify-end">
                                    <v-btn 
                                        icon="mdi-eye" 
                                        size="small" 
                                        variant="text"
                                        color="primary"
                                        :title="$t('collections.buttons.show')"
                                        @click.stop="router.visit(route('collections.show', collection.id))"
                                    />
                                    <v-btn 
                                        v-if="props.can.update"
                                        icon="mdi-pencil" 
                                        size="small" 
                                        variant="text"
                                        color="primary"
                                        :title="$t('collections.buttons.edit')"
                                        @click.stop="router.visit(route('collections.edit', collection.id))"
                                    />
                                    <v-btn 
                                        v-if="props.can.delete" 
                                        icon="mdi-delete" 
                                        size="small" 
                                        variant="text"
                                        color="error"
                                        @click.stop="confirmDelete(collection)"
                                        :title="$t('collections.buttons.delete')"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card>

            <!-- Mobile Cards -->
            <div class="d-md-none">
                <v-card
                    v-for="collection in props.collections.data" 
                    :key="collection.id" 
                    class="mb-3 collection-card"
                    elevation="2"
                    rounded="lg"
                    @click="router.visit(route('collections.show', collection.id))"
                >
                    <v-card-text class="pb-2">
                        <div class="d-flex align-start justify-space-between mb-2">
                            <div class="flex-grow-1 mr-2">
                                <h3 class="text-h6 mb-1 collection-name">{{ collection.name }}</h3>
                                <p v-if="collection.description" class="text-body-2 text-medium-emphasis mb-2">
                                    {{ collection.description.substring(0, 80) + (collection.description.length > 80 ? '...' : '') }}
                                </p>
                            </div>
                            <div class="d-flex flex-column align-end gap-1">
                                <v-chip
                                    :color="collection.is_public ? 'success' : 'warning'"
                                    size="x-small"
                                    variant="tonal"
                                    @click.stop="toggleVisibility(collection)"
                                    :loading="collection.updating_visibility"
                                    :disabled="collection.updating_visibility || !props.can.update"
                                >
                                    {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
                                </v-chip>
                                <v-btn
                                    v-if="collection.is_default"
                                    icon="mdi-star"
                                    color="primary"
                                    variant="text"
                                    size="x-small"
                                    :title="$t('collections.buttons.unset_default')"
                                    @click.stop="toggleDefault(collection)"
                                    :loading="collection.updating_default"
                                    :disabled="collection.updating_default || !props.can.setDefault"
                                />
                            </div>
                        </div>
                    </v-card-text>
                    
                    <v-card-actions class="pt-0">
                        <v-btn 
                            variant="text"
                            color="primary"
                            size="small"
                            prepend-icon="mdi-eye"
                            @click.stop="router.visit(route('collections.show', collection.id))"
                        >
                            {{ $t('collections.buttons.show') }}
                        </v-btn>
                        
                        <v-spacer />
                        
                        <v-btn 
                            v-if="props.can.update"
                            icon="mdi-pencil" 
                            size="small" 
                            variant="text"
                            color="primary"
                            :title="$t('collections.buttons.edit')"
                            @click.stop="router.visit(route('collections.edit', collection.id))"
                        />
                        <v-btn 
                            v-if="props.can.delete" 
                            icon="mdi-delete" 
                            size="small" 
                            variant="text"
                            color="error"
                            :title="$t('collections.buttons.delete')"
                            @click.stop="confirmDelete(collection)"
                        />
                    </v-card-actions>
                </v-card>
            </div>
        </div>
        
        <!-- Better Pagination -->
        <div v-if="props.collections && props.collections.meta && props.collections.meta.links.length > 3" 
             class="d-flex justify-center mt-6">
            <v-pagination
                :model-value="props.collections.meta.current_page"
                :length="props.collections.meta.last_page"
                @update:model-value="(page) => router.get(route('collections.index', { page }))"
                :total-visible="isMobile ? 3 : 7"
                rounded="circle"
                color="primary"
                variant="elevated"
            />
        </div>

        <!-- Dialog pro potvrzení smazání -->
        <v-dialog v-model="showDeleteDialog" max-width="400" :fullscreen="isMobile">
            <v-card v-if="selectedCollection" rounded="lg">
                <v-card-title class="text-h6 d-flex align-center">
                    <v-icon class="me-2" color="error">mdi-delete-alert</v-icon>
                    {{ $t('collections.delete_dialog.title') }}
                </v-card-title>
                <v-card-text>
                    <p class="text-body-1 mb-2">{{ $t('collections.delete_dialog.message', { name: selectedCollection.name }) }}</p>
                    <v-alert type="warning" variant="tonal" density="compact">
                        {{ $t('collections.delete_dialog.warning') }}
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn color="default" variant="text" @click="showDeleteDialog = false" :block="isMobile">
                        {{ $t('collections.buttons.cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="elevated" @click="deleteCollection" :loading="isDeleting" :block="isMobile">
                        {{ $t('collections.buttons.delete') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>



        <!-- Scroll to top button -->
        <v-fab
            v-show="showScrollButton"
            location="bottom end"
            size="small"
            color="surface-variant"
            icon="mdi-arrow-up"
            @click="scrollToTop"
        />
    </v-container>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';
import { ref, computed, onMounted, onUnmounted, getCurrentInstance } from 'vue';
import { useDisplay } from 'vuetify';
import { useScrollPerformance } from '@/utils/performance';

// Components
// import ConfirmDeleteDialog from '@/Components/Dialogs/ConfirmDeleteDialog.vue';

const auth = useAuthStore();
const notificationStore = useNotificationStore();
const { mobile: isMobile } = useDisplay();
const { addScrollListener, removeScrollListener, cleanup } = useScrollPerformance();

// Translation function
const instance = getCurrentInstance();
const $t = instance?.appContext.config.globalProperties.$t;

const props = defineProps({
    collections: Object, // Očekáváme paginovaná data
    can: Object, // Prop pro oprávnění specifická pro tuto stránku (z controlleru)
});

const showDeleteDialog = ref(false);
const selectedCollection = ref(null);
const showScrollButton = ref(false);
const isDeleting = ref(false);

const totalCollections = computed(() => {
    return props.collections?.total || 0;
});

const handleScroll = () => {
    if (window.scrollY > 200) {
        showScrollButton.value = true;
    } else {
        showScrollButton.value = false;
    }
};

onMounted(() => {
    addScrollListener(handleScroll);
});

onUnmounted(() => {
    removeScrollListener(handleScroll);
    cleanup();
});

const confirmDelete = (collection) => {
    selectedCollection.value = collection;
    showDeleteDialog.value = true;
};

const deleteCollection = () => {
    if (selectedCollection.value) {
        isDeleting.value = true;
        // TODO: Add retry mechanism for delete operations
        // TODO: Implement confirmation with undo functionality
        router.delete(route('collections.destroy', selectedCollection.value.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                notificationStore.success($t('ui.notifications.success.collection_deleted'));
                showDeleteDialog.value = false;
                selectedCollection.value = null;
            },
            onError: (errors) => {
                console.error('Error deleting collection:', errors);
                // TODO: Distinguish between recoverable and non-recoverable errors
                notificationStore.error($t('ui.notifications.error.collection_delete_failed'));
            },
            onFinish: () => {
                isDeleting.value = false;
            }
        });
    }
};

const toggleDefault = (collection) => {
    collection.updating_default = true;
    const newDefaultStatus = !collection.is_default;

    router.patch(route('collections.toggle_default', collection.id), {
        is_default: newDefaultStatus
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success($t('ui.notifications.success.collection_default_set'));
        },
        onError: (errors) => {
            console.error('Error updating default status:', errors);
            notificationStore.error($t('ui.notifications.error.collection_default_failed'));
        },
        onFinish: () => {
            collection.updating_default = false;
        }
    });
};

const toggleVisibility = (collection) => {
    collection.updating_visibility = true;
    const newPublicStatus = !collection.is_public;

    router.patch(route('collections.toggle_visibility', collection.id), {
        is_public: newPublicStatus
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success($t('ui.notifications.success.collection_visibility_changed'));
        },
        onError: (errors) => {
            console.error('Error updating visibility:', errors);
            notificationStore.error($t('ui.notifications.error.collection_visibility_failed'));
        },
        onFinish: () => {
            collection.updating_visibility = false;
        }
    });
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

// Překlady pro titulky a texty (příklad, doplňte podle potřeby v lang souborech)
// např. lang/cs/collections.php
// 'title' => 'Správa sbírek',
// 'buttons' => ['create' => 'Vytvořit sbírku', 'edit' => 'Upravit', 'delete' => 'Smazat', 'show' => 'Detail'],
// 'fields' => ['name' => 'Název', 'description' => 'Popis', 'is_default' => 'Výchozí', 'visibility' => 'Viditelnost', 'actions' => 'Akce'],
// 'visibility_values' => ['public' => 'Veřejná', 'private' => 'Soukromá'],
// 'messages' => ['no_collections' => 'Zatím nemáte vytvořenou žádnou sbírku.', 'deleted_successfully' => 'Sbírka byla úspěšně smazána.', 'delete_failed' => 'Nepodařilo se smazat sbírku.', 'update_default_failed' => 'Nepodařilo se aktualizovat výchozí stav.', 'update_visibility_failed' => 'Nepodařilo se aktualizovat viditelnost.'],
// 'dialogs' => [ 'delete' => [ 'title' => 'Potvrdit smazání sbírky' ]],
// 'actions' => ['set_default' => 'Nastavit jako výchozí', 'unset_default' => 'Zrušit jako výchozí', 'toggle_visibility' => 'Přepnout viditelnost']

</script>

<style scoped>


.collection-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.collection-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.collection-name {
    line-height: 1.2;
}

.hover-row:hover {
    background-color: rgba(var(--v-theme-primary), 0.04) !important;
}

.gap-3 {
    gap: 12px;
}

.gap-1 {
    gap: 4px;
}
</style> 