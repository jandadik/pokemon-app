<template>
  <v-select
    :label="$t('collections.form.language')"
    :items="languages"
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
const languageMapping = {
  'en': 'english',
  'de': 'german',
  'fr': 'french', 
  'cs': 'czech',
  'jp': 'japanese'
};

const languageKeys = ['en', 'de', 'fr', 'cs', 'jp'];
const languages = computed(() => languageKeys.map(key => ({
  value: languageMapping[key], // Použijeme databázovou hodnotu
  title: $t('collections.form.languages.' + key)
})));
</script> 