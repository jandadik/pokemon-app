<template>
  <v-text-field
    :label="$t('collections.form.quantity')"
    type="number"
    min="1"
    max="999"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :error-messages="errorMessages"
    required
  />
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:modelValue']);
</script> 