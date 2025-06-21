<template>
  <div>
    <CollectionItemForm
      :card="selectedCard"
      :variant="selectedVariant"
      :mode="formMode"
      :initial-data="initialData"
      @submit="handleSubmit"
      @cancel="handleCancel"
    />
    <div v-if="submitted" class="mt-6">
      <v-alert type="success">Odesláno! Data:
        <pre>{{ JSON.stringify(submitted, null, 2) }}</pre>
      </v-alert>
    </div>
    <div v-if="cancelled" class="mt-6">
      <v-alert type="info">Formulář byl zrušen.</v-alert>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import CollectionItemForm from './CollectionItemForm.vue';

const selectedCard = ref({
  id: 'sv3pt5-4',
  name: 'Charmander',
});
const selectedVariant = ref({
  id: 733599,
  name: 'Normal',
  image: 'https://assets.pokemon.com/assets/cms2/img/cards/web/SM1/SM1_EN_4.png',
});
const formMode = ref('create');
const initialData = ref({}); // Pro editaci můžeš předvyplnit

const submitted = ref(null);
const cancelled = ref(false);

function handleSubmit(data) {
  submitted.value = data;
  cancelled.value = false;
}
function handleCancel() {
  cancelled.value = true;
  submitted.value = null;
}
</script> 