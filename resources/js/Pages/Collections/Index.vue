<template>
    <v-container>
        <Head :title="$t('collections.title')" />

        <!-- Hlavička stránky -->
        <v-card class="mb-4 page-header" id="top">
            <v-card-text>
                <v-row align="center">
                    <v-col cols="12" md="2" class="d-flex justify-center align-center">
                        <div class="page-logo-container">
                            <v-icon
                                size="64"
                                color="primary"
                                icon="mdi-folder-multiple-outline"
                                class="mx-auto"
                            />
                        </div>
                    </v-col>
                    <v-col cols="12" md="10">
                        <div class="d-flex flex-wrap align-center justify-space-between">
                            <h1 class="text-h4">{{ $t('collections.title') }}</h1>
                            
                            <div class="d-flex align-center">
                                <v-chip
                                    v-if="props.collections && props.collections.total !== undefined"
                                    class="me-2"
                                    color="primary"
                                    variant="outlined"
                                >
                                    {{ props.collections.total }} {{ $t('collections.collections_count_text', props.collections.total) }}
                                </v-chip>
                                
                                <v-btn
                                    v-if="auth.can('collections.create') || props.can?.create_collection"
                                    @click="router.get(route('collections.create'))"
                                    color="primary"
                                >
                                    {{ $t('collections.buttons.create') }}
                                </v-btn>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <p v-if="!props.collections || !props.collections.data || props.collections.data.length === 0" class="text-center text-grey-darken-1 my-10">
            {{ $t('collections.empty.text') }}
        </p>

        <v-table v-else fixed-header hover class="elevation-1">
            <thead>
                <tr>
                    <th class="text-left">{{ $t('collections.fields.name') }}</th>
                    <th class="text-left">{{ $t('collections.fields.description') }}</th>
                    <th class="text-center">{{ $t('collections.fields.is_default') }}</th>
                    <th class="text-center">{{ $t('collections.fields.visibility') }}</th>
                    <th class="text-right">{{ $t('collections.fields.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="collection in props.collections.data" :key="collection.id">
                    <td>
                        <div 
                            class="collection-name cursor-pointer"
                            @click="router.visit(route('collections.show', collection.id))"
                        >
                            {{ collection.name }}
                        </div>
                    </td>
                    <td>
                        <div 
                            class="collection-description cursor-pointer"
                            @click="router.visit(route('collections.show', collection.id))"
                        >
                            {{ collection.description ? collection.description.substring(0, 50) + (collection.description.length > 50 ? '...' : '') : '-' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <v-btn
                            :icon="collection.is_default ? 'mdi-star' : 'mdi-star-outline'"
                            :color="collection.is_default ? 'primary' : 'grey'"
                            variant="text"
                            size="small"
                            @click="toggleDefault(collection)"
                            :loading="collection.updating_default" 
                            :disabled="collection.updating_default || !props.can.setDefault"
                            :title="collection.is_default ? $t('collections.buttons.unset_default') : $t('collections.buttons.set_default')"
                        ></v-btn>
                    </td>
                    <td class="text-center">
                        <v-btn
                            @click="toggleVisibility(collection)"
                            :color="collection.is_public ? 'success' : 'warning'"
                            size="small"
                            variant="tonal"
                            :loading="collection.updating_visibility"
                            :disabled="collection.updating_visibility || !props.can.update"
                            :title="$t('collections.buttons.toggle_visibility')"
                        >
                            {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
                        </v-btn>
                    </td>
                    <td class="text-right">
                        <v-btn 
                            icon="mdi-eye" 
                            size="x-small" 
                            variant="text"
                            color="orange"
                            class="mr-1"
                            :title="$t('collections.buttons.show')"
                            @click="router.visit(route('collections.show', collection.id))"
                        ></v-btn>
                        <v-btn 
                            v-if="props.can.update"
                            icon="mdi-pencil" 
                            size="x-small" 
                            variant="text"
                            color="primary"
                            class="mr-1"
                            :title="$t('collections.buttons.edit')"
                            @click="router.visit(route('collections.edit', collection.id))"
                        ></v-btn>
                        <v-btn 
                            v-if="props.can.delete" 
                            icon="mdi-delete" 
                            size="x-small" 
                            variant="text"
                            color="error"
                            @click="confirmDelete(collection)"
                            :title="$t('collections.buttons.delete')"
                        ></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>
        
        <!-- Paginace -->
        <div v-if="props.collections && props.collections.meta && props.collections.meta.links.length > 3" class="text-center mt-6">
            <Link
                v-for="(link, index) in props.collections.meta.links"
                :key="index"
                :href="link.url"
                class="px-1"
                :class="{ 'v-btn v-btn--active mx-1 v-btn--variant-tonal primary': link.active, 'v-btn v-btn--disabled mx-1 v-btn--variant-tonal': !link.url, 'v-btn mx-1 v-btn--variant-tonal': link.url && !link.active }"
                preserve-scroll
            >
                <span v-html="link.label"></span>
            </Link>
        </div>

        <!-- Dialog pro potvrzení smazání -->
        <v-dialog v-model="showDeleteDialog" max-width="500">
            <v-card v-if="selectedCollection">
                <v-card-title class="text-h5">
                    {{ $t('collections.delete_dialog.title') }}
                </v-card-title>
                <v-card-text>
                    {{ $t('collections.delete_dialog.message', { name: selectedCollection.name }) }}
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="default" variant="text" @click="showDeleteDialog = false">
                        {{ $t('collections.buttons.cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="tonal" @click="deleteCollection" :loading="isDeleting">
                        {{ $t('collections.buttons.delete') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Plovoucí tlačítko pro přesun zpět nahoru -->
        <v-btn
            v-show="showScrollButton"
            icon="mdi-arrow-up"
            class="scroll-to-top"
            color="primary"
            elevation="8"
            size="large"
            :aria-label="$t('ui.buttons.scroll_to_top')"
            @click="scrollToTop"
        ></v-btn>
    </v-container>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore';
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Components
// import ConfirmDeleteDialog from '@/Components/Dialogs/ConfirmDeleteDialog.vue';


const auth = useAuthStore();

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
    window.addEventListener('scroll', handleScroll);
});

// Přidat onUnmounted pro odstranění listeneru
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const confirmDelete = (collection) => {
    selectedCollection.value = collection;
    showDeleteDialog.value = true;
};

const deleteCollection = () => {
    if (selectedCollection.value) {
        isDeleting.value = true;
        router.delete(route('collections.destroy', selectedCollection.value.id), {
            preserveState: true, // Zachová stav stránky
            preserveScroll: true,
            onSuccess: () => {
                // Můžeme přidat notifikaci o úspěšném smazání
                // Seznam by se měl automaticky obnovit díky Inertia partial reload
                console.log('Collection deleted:', selectedCollection.value.id);
                // Případně zavolat globální notifikaci
                // např. toastStore.show({ message: 'Collection deleted successfully.', type: 'success' });
                showDeleteDialog.value = false;
                selectedCollection.value = null;
            },
            onError: (errors) => {
                // Zpracování chyb, zobrazení notifikace
                console.error('Error deleting collection:', errors);
                // např. toastStore.show({ message: 'Failed to delete collection.', type: 'error' });
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
            // props.collections by se měl automaticky aktualizovat
            // Pokud by se is_default nastavovalo na true, ostatní by se na serveru měly nastavit na false
            // např. toastStore.show({ message: 'Default status updated.', type: 'success' });
        },
        onError: (errors) => {
            console.error('Error updating default status:', errors);
            // Zobrazit chybovou notifikaci
            // Např. toastStore.show({ message: 'Failed to update default status.', type: 'error' });
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
            // props.collections by se měl automaticky aktualizovat
            // např. toastStore.show({ message: 'Visibility updated.', type: 'success' });
        },
        onError: (errors) => {
            console.error('Error updating visibility:', errors);
            // Zobrazit chybovou notifikaci
            // Např. toastStore.show({ message: 'Failed to update visibility.', type: 'error' });
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
.scroll-to-top-btn {
    position: fixed;
    bottom: 20px;
    right: 20px; 
    z-index: 100;
}
/* Styly specifické pro tuto stránku, pokud jsou potřeba */
.cursor-pointer {
    cursor: pointer;
}

.collection-name {
    transition: color 0.2s ease;
}

.collection-name:hover {
    color: #1976D2; /* Primární modrá barva - přizpůsobte dle vašeho barevného schématu */
}

.collection-description {
    transition: color 0.2s ease;
}

.collection-description:hover {
    color: #1976D2; /* Primární modrá barva - přizpůsobte dle vašeho barevného schématu */
}
</style> 