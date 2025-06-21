<template>
  <v-textarea
    :label="$t('collections.form.note')"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :error-messages="errorMessages"
    maxlength="500"
    rows="2"
    auto-grow
  />
</template>

<script setup>
const props = defineProps({
  modelValue: String,
  errorMessages: { type: [String, Array], default: '' }
});
const emit = defineEmits(['update:modelValue']);
</script> 