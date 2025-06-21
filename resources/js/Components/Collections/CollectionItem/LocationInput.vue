<template>
  <v-text-field
    :label="$t('collections.form.location')"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :error-messages="errorMessages"
    maxlength="100"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: String,
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:modelValue']);
</script> 