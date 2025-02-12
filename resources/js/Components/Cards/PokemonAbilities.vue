<template>
  <v-card v-if="abilities?.length" class="mb-4">
    <v-card-title>SCHOPNOSTI</v-card-title>
    <v-card-text>
      <div class="d-flex flex-column gap-4">
        <div v-for="ability in abilities" :key="ability.name">
          <div class="d-flex justify-space-between align-center">
            <div class="d-flex align-center gap-2">
              <span class="text-h6">{{ ability.name }}</span>
              <span class="text-caption text-grey">({{ ability.type }})</span>
            </div>
          </div>
          <div v-if="ability.text" class="mt-1 text-grey-darken-1">
            {{ ability.text }}
          </div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
const props = defineProps({
  abilities: {
    type: Array,
    required: true
  }
})
</script>

<style scoped>
.abilities-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
</style>
