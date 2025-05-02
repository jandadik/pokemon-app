<template>
    <Head :title="$t('collections.title')" />

    <div class="d-flex justify-space-between align-center mb-4">
        <h1>{{ $t('collections.title') }}</h1>
        <!-- Tlačítko pro vytvoření nové sbírky (pokud má oprávnění) -->
        <v-btn 
            v-if="auth.can('collections.create') || can_prop.create_collection" 
            color="primary"
            @click="showCreateDialog = true" 
        >
            {{ $trans('collections.buttons.create') }} 
        </v-btn>
    </div>

    <p>Tato stránka bude zobrazovat seznam vašich sbírek.</p>

    <!-- Příklad výpisu sbírek předaných z controlleru -->
    <div v-if="collections.length > 0">
        <h2>Seznam sbírek:</h2>
        <ul>
            <li v-for="collection in collections" :key="collection.id">
                {{ collection.name }} (ID: {{ collection.id }}, Default: {{ collection.is_default }})
                <!-- Zde přidáme tlačítka pro úpravu/smazání s v-if auth.can(...) -->
                <v-btn 
                    v-if="auth.can('collections.edit.own') || auth.can('collections.edit.any')" 
                    icon="mdi-pencil" 
                    size="x-small" 
                    variant="text"
                    @click="editCollection(collection)">
                </v-btn>
                <v-btn 
                    v-if="auth.can('collections.delete.own') || auth.can('collections.delete.any')" 
                    icon="mdi-delete" 
                    size="x-small" 
                    variant="text"
                    color="error"
                    @click="confirmDelete(collection)">
                </v-btn>
            </li>
        </ul>
    </div>
    <div v-else>
        <p>Zatím nemáte vytvořenou žádnou sbírku.</p>
    </div>

    <!-- TODO: Dialogy pro vytvoření, úpravu a smazání sbírky -->
    <!-- <CreateCollectionDialog v-model="showCreateDialog" /> -->
    <!-- <EditCollectionDialog v-model="showEditDialog" :collection="selectedCollection" /> -->
    <!-- <ConfirmDeleteDialog v-model="showDeleteDialog" @confirm="deleteCollection" /> -->

</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'; // Import auth store
import { ref } from 'vue';

const auth = useAuthStore(); // Instance store

const props = defineProps({
    collections: Array, // Definujeme prop pro sbírky
    can: Object, // Prop pro oprávnění specifická pro tuto stránku (z controlleru)
});

// Reference pro zobrazení dialogů a vybranou sbírku
const showCreateDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedCollection = ref(null);

// Placeholder funkce pro akce
const editCollection = (collection) => {
    selectedCollection.value = collection;
    showEditDialog.value = true;
    console.log('Edit collection:', collection.id);
};

const confirmDelete = (collection) => {
    selectedCollection.value = collection;
    showDeleteDialog.value = true;
    console.log('Confirm delete collection:', collection.id);
};

const deleteCollection = () => {
    console.log('Deleting collection:', selectedCollection.value.id);
    // Zde by byla logika pro odeslání požadavku na smazání
    showDeleteDialog.value = false;
};

// TODO: Přidat překlady do collections.php pro tlačítko 'create'

</script>

<style scoped>
/* Styly specifické pro tuto stránku */
li {
    margin-bottom: 8px; /* Malý odstup mezi položkami seznamu */
}
</style> 