<template>
  <v-list-item
    :class="{ 'v-list-item--active': isSelected }"
    @click="$emit('click')"
    :disabled="disabled"
    density="compact"
  >
    <template #prepend>
      <v-icon 
        :color="isSelected ? 'primary' : 'default'"
        :icon="isSelected ? 'mdi-radiobox-marked' : 'mdi-radiobox-blank'"
      />
    </template>
    
    <v-list-item-title>{{ variantType.name }}</v-list-item-title>
    
    <v-list-item-subtitle v-if="variantType.description">
      {{ variantType.description }}
    </v-list-item-subtitle>
  </v-list-item>
</template>

<script setup>
defineProps({
  variantType: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

defineEmits(['click']);
</script> 