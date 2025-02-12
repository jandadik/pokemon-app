<template>
  <v-card v-if="rules?.length" class="mb-4">
    <v-card-title>PRAVIDLA</v-card-title>
    <v-card-text>
      <div class="d-flex flex-column gap-4">
        <div v-for="(rule, index) in rules" :key="index">
          <div class="text-body-1">{{ rule }}</div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
const props = defineProps({
  rules: {
    type: Array,
    required: true
  }
})
</script>

<style scoped>
.rules-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
</style>
