<template>
  <v-select
    :label="$t('collections.form.condition')"
    :items="conditions"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :error-messages="errorMessages"
    required
  />
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';

const props = defineProps({
  modelValue: String,
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:modelValue']);

// Získání globální $t() funkce přes getCurrentInstance podle pravidel
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

// Mapování zkratek na databázové hodnoty
const conditionMapping = {
  'nm': 'near_mint',
  'ex': 'excellent', 
  'gd': 'good',
  'pl': 'played',
  'po': 'poor'
};

const conditionKeys = ['nm', 'ex', 'gd', 'pl', 'po'];
const conditions = computed(() => conditionKeys.map(key => ({
  value: conditionMapping[key], // Použijeme databázovou hodnotu
  title: $t('collections.form.conditions.' + key)
})));
</script> 