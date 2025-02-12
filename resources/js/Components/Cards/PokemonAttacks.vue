<template>
  <v-card v-if="attacks?.length" class="mb-4">
    <v-card-title>ÚTOKY</v-card-title>
    <v-card-text>
      <div class="d-flex flex-column gap-4">
        <div v-for="attack in attacks" :key="attack.name">
          <div class="d-flex justify-space-between align-center">
            <div class="d-flex align-center gap-2">
              <div class="d-flex gap-1">
                <v-img
                  v-for="(cost, index) in getEnergyCosts(attack.cost)"
                  :key="`${attack.name}-${cost}-${index}`"
                  :src="`/images/energy/${cost.toLowerCase()}.png`"
                  :alt="cost"
                  width="24"
                  height="24"
                />
              </div>
              <span class="text-h6">{{ attack.name }}</span>
            </div>
            <span class="text-h6">{{ attack.damage }}</span>
          </div>
          <div v-if="attack.text" class="mt-1 ml-8 text-grey-darken-1">
            {{ attack.text }}
          </div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  cardId: {
    type: String,
    required: true
  }
})

const attacks = ref([])

const getEnergyCosts = (costJson) => {
  if (!costJson) return []
  try {
    return JSON.parse(costJson)
  } catch (e) {
    console.error('Error parsing energy costs:', e)
    return []
  }
}

onMounted(async () => {
  try {
    const response = await fetch(`/api/cards/${props.cardId}/attacks`)
    const data = await response.json()
    if (data.data) {
      attacks.value = data.data
    }
  } catch (error) {
    console.error('Error fetching attacks:', error)
  }
})
</script>

<style scoped>
.attacks-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
</style>