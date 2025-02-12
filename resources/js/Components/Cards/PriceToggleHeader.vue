<template>
    <div class="flex items-center gap-2 mb-4">
      <h2 class="text-lg font-bold mb-4">PRICES</h2>
      <div class="flex items-center gap-4 ml-4">
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            :checked="modelValue.tcg"
            @change="$emit('update:modelValue', { ...modelValue, tcg: !modelValue.tcg })"
            class="w-4 h-4 text-blue-600"
          >
          <span class="text-sm text-gray-700">TCGPlayer</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            :checked="modelValue.cardmarket"
            @change="$emit('update:modelValue', { ...modelValue, cardmarket: !modelValue.cardmarket })"
            class="w-4 h-4 text-blue-600"
          >
          <span class="text-sm text-gray-700">Cardmarket</span>
        </label>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    modelValue: {
      type: Object,
      required: true
    }
  })
  
  defineEmits(['update:modelValue']);
  </script>