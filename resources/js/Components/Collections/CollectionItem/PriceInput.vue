<template>
  <v-text-field
    :label="$t('collections.form.purchase_price')"
    type="number"
    min="0"
    step="0.01"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :error-messages="errorMessages"
    prefix="€"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:modelValue']);
</script> 