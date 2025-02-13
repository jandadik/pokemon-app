<template>
    <v-card-title class="d-flex justify-space-between align-center">
        Číselníky
        <v-btn
            color="primary"
            @click="dialog = true"
        >
            Přidat kategorii
        </v-btn>
    </v-card-title>

    <v-card-text>
        <v-data-table
            :headers="headers"
            :items="categories"
            class="elevation-1"
        >
            <template v-slot:item.actions="{ item }">
                <v-btn
                    size="small"
                    color="primary"
                    class="me-2"
                    @click="navigateToRegisters(item)"
                >
                    Položky
                </v-btn>
                <v-icon
                    size="small"
                    class="me-2"
                    @click="editCategory(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon
                    size="small"
                    @click="confirmDelete(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
        </v-data-table>
    </v-card-text>

    <!-- Dialog pro položky číselníku -->
    <v-dialog v-model="dialogItems" max-width="900px">
        <v-card>
            <RegisterItems 
                v-if="selectedCategory"
                :category="selectedCategory"
                :registers="selectedCategory.registers"
            />
        </v-card>
    </v-dialog>

    <!-- Dialog pro přidání/úpravu kategorie -->
    <v-dialog v-model="dialog" max-width="500px">
        <v-card>
            <v-card-title>
                {{ editedItem.id ? 'Upravit kategorii' : 'Nová kategorie' }}
            </v-card-title>
            
            <v-card-text>
                <v-form @submit.prevent="save">
                    <v-text-field
                        v-model="editedItem.name"
                        label="Název"
                        required
                    />
                    <v-text-field
                        v-model="editedItem.type"
                        label="Typ"
                        required
                    />
                </v-form>
            </v-card-text>
            
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="grey-darken-1"
                    variant="text"
                    @click="close"
                >
                    Zrušit
                </v-btn>
                <v-btn
                    color="primary"
                    variant="text"
                    @click="save"
                >
                    Uložit
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="dialogDelete" max-width="500px">
        <v-card>
            <v-card-title>Opravdu chcete smazat tuto kategorii?</v-card-title>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="grey-darken-1"
                    variant="text"
                    @click="dialogDelete = false"
                >
                    Zrušit
                </v-btn>
                <v-btn
                    color="error"
                    variant="text"
                    @click="deleteItemConfirm"
                >
                    Smazat
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import RegisterItems from '@/Components/Admin/RegisterItems.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

const headers = [
    { title: 'Název', key: 'name' },
    { title: 'Typ', key: 'type' },
    { title: 'Akce', key: 'actions', sortable: false }
];

const dialog = ref(false);
const dialogDelete = ref(false);
const dialogItems = ref(false);
const editedItem = ref({
    id: null,
    name: '',
    type: ''
});
const deletedItem = ref(null);
const selectedCategory = ref(null);

const navigateToRegisters = (item) => {
    if (item && item.id) {
        router.visit(route('registers.index', item.id));
    }
};

const editCategory = (item) => {
    editedItem.value = { ...item };
    dialog.value = true;
};

const confirmDelete = (item) => {
    deletedItem.value = item;
    dialogDelete.value = true;
};

const deleteItemConfirm = () => {
    router.delete(route('register-categories.destroy', deletedItem.value.id), {
        onSuccess: () => {
            dialogDelete.value = false;
        }
    });
};

const close = () => {
    dialog.value = false;
    editedItem.value = {
        id: null,
        name: '',
        type: ''
    };
};

const save = () => {
    if (editedItem.value.id) {
        router.put(route('register-categories.update', editedItem.value.id), editedItem.value, {
            onSuccess: () => {
                close();
            }
        });
    } else {
        router.post(route('register-categories.store'), editedItem.value, {
            onSuccess: () => {
                close();
            }
        });
    }
};
</script> 