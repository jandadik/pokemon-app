<template>
  <Edit :permissions="permissions" />
</template>

<script setup>
import Edit from './Edit.vue'

defineProps({
  permissions: {
    type: Array,
    required: true
  }
})
</script> 