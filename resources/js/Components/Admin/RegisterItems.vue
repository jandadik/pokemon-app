<template>
    <v-card-title class="d-flex justify-space-between align-center">
        Položky číselníku {{ category.name }}
        <v-btn
            color="primary"
            @click="dialog = true"
        >
            Přidat položku
        </v-btn>
    </v-card-title>

    <v-card-text>
        <v-data-table
            :headers="headers"
            :items="registers || []"
            class="elevation-1"
        >
            <template #[`item.default`]="{ item }">
                <v-checkbox
                    :model-value="!!item.default"
                    disabled
                />
            </template>
            <template #[`item.actions`]="{ item }">
                <v-icon
                    size="small"
                    class="me-2"
                    @click="editItem(item)"
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

    <!-- Dialog pro editaci/přidání -->
    <v-dialog v-model="dialog" max-width="500px">
        <v-card>
            <v-card-title>
                <span>{{ editedItem.id ? 'Upravit' : 'Přidat' }} položku</span>
            </v-card-title>

            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                v-model="editedItem.name"
                                label="Název"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="editedItem.type"
                                label="Typ"
                            />
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox
                                v-model="editedItem.default"
                                label="Výchozí hodnota"
                            />
                        </v-col>
                    </v-row>
                </v-container>
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
            <v-card-title>Opravdu chcete smazat tuto položku?</v-card-title>
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

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    registers: {
        type: Array,
        default: () => []
    }
});

const headers = [
    { title: 'Název', key: 'name' },
    { title: 'Typ', key: 'type' },
    { title: 'Výchozí', key: 'default' },
    { title: 'Akce', key: 'actions', sortable: false }
];

const dialog = ref(false);
const dialogDelete = ref(false);
const editedItem = ref({
    id: null,
    name: '',
    type: '',
    default: false,
    register_category_id: props.category.id
});
const deletedItem = ref(null);

const editItem = (item) => {
    editedItem.value = { 
        id: item.id,
        name: item.name,
        type: item.type,
        default: !!item.default,
        register_category_id: props.category.id
    };
    dialog.value = true;
};

const confirmDelete = (item) => {
    deletedItem.value = item;
    dialogDelete.value = true;
};

const deleteItemConfirm = () => {
    router.delete(route('registers.destroy', deletedItem.value.id), {
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
        type: '',
        default: false,
        register_category_id: props.category.id
    };
};

const save = () => {
    if (editedItem.value.id) {
        router.put(route('registers.update', editedItem.value.id), editedItem.value, {
            onSuccess: () => {
                close();
            }
        });
    } else {
        router.post(route('registers.store'), editedItem.value, {
            onSuccess: () => {
                close();
            }
        });
    }
};
</script> 