<template>
    <v-container>
        <Head :title="collection ? collection.name : $t('collections.titles.detail')" />

        <!-- Hlavička stránky -->
        <v-card class="mb-4 page-header">
            <v-card-text>
                <v-row align="center">
                    <v-col cols="12" md="2" class="d-flex justify-center align-center">
                        <div class="page-logo-container">
                            <v-icon
                                size="64"
                                color="primary"
                                icon="mdi-folder-open-outline" 
                                class="mx-auto"
                            />
                        </div>
                    </v-col>
                    <v-col cols="12" md="10">
                        <div class="d-flex align-center">
                            <h1 class="text-h4">{{ collection ? collection.name : $t('collections.titles.detail') }}</h1>
                            <v-chip 
                                v-if="collection" 
                                class="ml-4" 
                                :color="collection.is_public ? 'success' : 'warning'" 
                                size="small"
                            >
                                {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
                            </v-chip>
                            <v-chip 
                                v-if="collection && collection.is_default" 
                                class="ml-2" 
                                color="primary" 
                                size="small"
                            >
                                {{ $t('collections.default_badge') }}
                            </v-chip>
                        </div>
                        <p v-if="collection && collection.description" class="text-grey mt-2">
                            {{ collection.description }}
                        </p>
                        <p v-else-if="collection" class="text-grey-darken-1 font-italic mt-2">
                            {{ $t('collections.no_description') }}
                        </p>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Akční tlačítka -->
        <v-card class="mb-4">
            <v-card-text>
                <div class="d-flex flex-wrap gap-2">
                    <v-btn 
                        v-if="can && can.edit" 
                        color="primary" 
                        prepend-icon="mdi-pencil"
                        variant="outlined"
                        @click="router.visit(route('collections.edit', collection.id))"
                    >
                        {{ $t('collections.buttons.edit') }}
                    </v-btn>

                    <v-btn 
                        v-if="can && can.delete" 
                        color="error" 
                        prepend-icon="mdi-delete" 
                        variant="outlined"
                        @click="confirmDelete = true"
                    >
                        {{ $t('collections.buttons.delete') }}
                    </v-btn>

                    <v-btn
                        v-if="can && can.toggleDefault && !collection.is_default"
                        color="secondary"
                        prepend-icon="mdi-star-outline"
                        variant="outlined"
                        @click="setAsDefault"
                        :loading="isSettingDefault"
                    >
                        {{ $t('collections.actions.set_as_default') }}
                    </v-btn>

                    <v-btn
                        v-if="can && can.toggleVisibility"
                        :color="collection.is_public ? 'warning' : 'success'"
                        :prepend-icon="collection.is_public ? 'mdi-eye-off' : 'mdi-eye'"
                        variant="outlined"
                        @click="toggleVisibility"
                        :loading="isTogglingVisibility"
                    >
                        {{ collection.is_public ? $t('collections.actions.make_private') : $t('collections.actions.make_public') }}
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>

        <!-- Obsah kolekce -->
        <v-card>
            <v-card-title class="d-flex align-center">
                {{ $t('collections.cards_in_collection') }} 
                <v-chip class="ml-2" color="info" size="small">
                    {{ collection && collection.cards ? collection.cards.length : 0 }}
                </v-chip>
            </v-card-title>
            
            <v-divider></v-divider>
            
            <v-card-text class="pt-4">
                <div v-if="collection && collection.cards && collection.cards.length > 0">
                    <!-- Zde později implementovat zobrazení karet v kolekci -->
                    <p>{{ $t('collections.cards_list_placeholder') }}</p>
                </div>
                <v-alert
                    v-else
                    type="info"
                    variant="tonal"
                    icon="mdi-information"
                >
                    {{ $t('collections.no_cards_in_collection') }}
                </v-alert>
            </v-card-text>
        </v-card>

        <!-- Dialog pro potvrzení smazání -->
        <v-dialog v-model="confirmDelete" max-width="500">
            <v-card>
                <v-card-title class="text-h5">
                    {{ $t('collections.delete_dialog.title') }}
                </v-card-title>
                <v-card-text>
                    {{ $t('collections.delete_dialog.message', { name: collection ? collection.name : '' }) }}
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="default" variant="text" @click="confirmDelete = false">
                        {{ $t('collections.buttons.cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="tonal" @click="deleteCollection" :loading="isDeleting">
                        {{ $t('collections.buttons.delete') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    collection: Object,
    can: Object,
});

// Stavy pro načítání
const isTogglingVisibility = ref(false);
const isSettingDefault = ref(false);
const isDeleting = ref(false);
const confirmDelete = ref(false);

// Metoda pro nastavení kolekce jako výchozí
const setAsDefault = () => {
    if (!props.collection) return;
    
    isSettingDefault.value = true;
    router.patch(route('collections.toggle_default', props.collection.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Stav kolekce bude aktualizován automaticky díky Inertia refresh
            isSettingDefault.value = false;
        },
        onError: () => {
            isSettingDefault.value = false;
            console.error('Chyba při nastavování kolekce jako výchozí');
        }
    });
};

// Metoda pro přepnutí viditelnosti kolekce
const toggleVisibility = () => {
    if (!props.collection) return;
    
    isTogglingVisibility.value = true;
    router.patch(route('collections.toggle_visibility', props.collection.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Stav kolekce bude aktualizován automaticky díky Inertia refresh
            isTogglingVisibility.value = false;
        },
        onError: () => {
            isTogglingVisibility.value = false;
            console.error('Chyba při přepínání viditelnosti kolekce');
        }
    });
};

// Metoda pro smazání kolekce
const deleteCollection = () => {
    if (!props.collection) return;
    
    isDeleting.value = true;
    router.delete(route('collections.destroy', props.collection.id), {
        onSuccess: () => {
            // Přesměrovat na seznam kolekcí po úspěšném smazání
            router.visit(route('collections.index'));
        },
        onError: () => {
            isDeleting.value = false;
            confirmDelete.value = false;
        }
    });
};
</script>

<style scoped>
.gap-2 {
    gap: 8px;
}
</style> 