<template>
    <v-card class="mb-4">
        <v-card-title class="d-flex justify-space-between align-center">
            Nastavení parametrů
            <v-btn
                color="primary"
                @click="addNewParameter"
                prepend-icon="mdi-plus"
            >
                Přidat parametr
            </v-btn>
        </v-card-title>

        <v-card-text>
            <v-table v-if="userParametersStore.parameters">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Hodnota</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value, key) in userParametersStore.parameters" :key="key">
                        <td>{{ key }}</td>
                        <td>
                            <v-text-field
                                v-model="userParametersStore.parameters[key]"
                                density="compact"
                                variant="outlined"
                                hide-details
                                @change="handleParameterChange"
                            />
                        </td>
                        <td>
                            <v-btn
                                icon="mdi-delete"
                                color="error"
                                variant="text"
                                size="small"
                                @click="deleteParameter(key)"
                            />
                        </td>
                    </tr>
                </tbody>
            </v-table>

            <v-alert
                v-else
                type="info"
                text="Zatím nejsou nastaveny žádné parametry"
            />
        </v-card-text>

        <!-- Dialog pro přidání nového parametru -->
        <v-dialog v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>Přidat nový parametr</v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="saveNewParameter">
                        <v-text-field
                            v-model="newParameter.key"
                            label="Název parametru"
                            required
                        />
                        <v-text-field
                            v-model="newParameter.value"
                            label="Hodnota parametru"
                            required
                        />
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn
                        color="grey"
                        text
                        @click="dialog = false"
                    >
                        Zrušit
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="saveNewParameter"
                        :disabled="!newParameter.key || !newParameter.value"
                    >
                        Uložit
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useUserParametersStore } from '@/stores/userParametersStore';

const userParametersStore = useUserParametersStore();
const dialog = ref(false);
const newParameter = ref({
    key: '',
    value: ''
});

// Přímý přístup ke store místo computed property
const saveNewParameter = () => {
    if (newParameter.value.key && newParameter.value.value) {
        const updatedParameters = {
            ...userParametersStore.parameters,
            [newParameter.value.key]: newParameter.value.value
        };
        userParametersStore.updateParameters(updatedParameters);
        dialog.value = false;
    }
};

const deleteParameter = (key) => {
    const updatedParameters = { ...userParametersStore.parameters };
    delete updatedParameters[key];
    userParametersStore.updateParameters(updatedParameters);
};

const handleParameterChange = () => {
    userParametersStore.updateParameters(userParametersStore.parameters);
};

// Přidání nového parametru
const addNewParameter = () => {
    newParameter.value = { key: '', value: '' };
    dialog.value = true;
};
</script>

<style scoped>
.v-table {
    width: 100%;
}
</style> 