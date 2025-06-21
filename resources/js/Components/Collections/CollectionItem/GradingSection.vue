<template>
  <v-row>
    <v-col cols="12">
      <v-select
        :label="$t('collections.form.grading')"
        :items="gradingAgencies"
        :model-value="grading"
        @update:model-value="$emit('update:grading', $event)"
        :error-messages="errorMessages"
      />
    </v-col>
    <v-col cols="12">
      <v-text-field
        :label="$t('collections.form.grading_cert')"
        :model-value="gradingCert"
        @update:model-value="$emit('update:gradingCert', $event)"
      />
    </v-col>
  </v-row>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';

const props = defineProps({
  grading: String,
  gradingCert: String,
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:grading', 'update:gradingCert']);

// Získání globální $t() funkce přes getCurrentInstance podle pravidel
const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

const gradingAgencyKeys = ['psa', 'bgs', 'cgc', 'sgc'];
const gradingAgencies = computed(() => gradingAgencyKeys.map(key => ({
  value: key,
  title: $t('collections.form.grading_agencies.' + key)
})));
</script> 