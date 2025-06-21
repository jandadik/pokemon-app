<template>
  <v-checkbox
    :label="$t('collections.form.first_edition')"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: Boolean
});
const emit = defineEmits(['update:modelValue']);
</script> 