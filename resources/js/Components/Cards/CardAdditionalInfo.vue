# CardAdditionalInfo.vue
<template>
  <v-card class="mb-4">
    <v-card-text>
      <v-row>
        <!-- Weakness -->
        <v-col cols="12" sm="6" lg="4">
          <v-card variant="outlined" height="100%">
            <v-card-title class="text-subtitle-1">Slabost</v-card-title>
            <v-card-text class="d-flex justify-start">
              <div v-if="weakness?.length" class="d-flex align-center">
                <v-img
                  :src="`/images/energy/${weakness[0].type.toLowerCase()}.png`"
                  :alt="weakness[0].type"
                  width="24"
                  height="24"
                  class="me-2"
                />
                <span>{{ weakness[0].value }}</span>
              </div>
              <span v-else class="text-grey">N/A</span>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Resistance -->
        <v-col cols="12" sm="6" lg="4">
          <v-card variant="outlined" height="100%">
            <v-card-title class="text-subtitle-1">Odolnost</v-card-title>
            <v-card-text class="d-flex justify-start">
              <div v-if="resistance?.length" class="d-flex align-center">
                <v-img
                  :src="`/images/energy/${resistance[0].type.toLowerCase()}.png`"
                  :alt="resistance[0].type"
                  width="24"
                  height="24"
                  class="me-2"
                />
                <span>{{ resistance[0].value }}</span>
              </div>
              <span v-else class="text-grey">N/A</span>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Retreat Cost -->
        <v-col cols="12" sm="6" lg="4">
          <v-card variant="outlined" height="100%">
            <v-card-title class="text-subtitle-1">Cena ústupu</v-card-title>
            <v-card-text class="d-flex justify-start">
              <div v-if="retreatCost?.length" class="d-flex align-center">
                <v-img
                  v-for="(cost, index) in retreatCost"
                  :key="index"
                  src="/images/energy/colorless.png"
                  alt="Colorless"
                  width="24"
                  height="24"
                  class="me-1"
                />
              </div>
              <span v-else class="text-grey">N/A</span>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Artist -->
        <v-col cols="12" sm="6" lg="4">
          <v-card variant="outlined" height="100%">
            <v-card-title class="text-subtitle-1">Ilustrátor</v-card-title>
            <v-card-text class="d-flex justify-start">
              <span v-if="artist">{{ artist }}</span>
              <span v-else class="text-grey">N/A</span>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Regulation Mark -->
        <v-col cols="12" sm="6" lg="4">
          <v-card variant="outlined" height="100%">
            <v-card-title class="text-subtitle-1">Regulační značka</v-card-title>
            <v-card-text class="d-flex justify-start">
              <div v-if="regulationMark" class="regulation-mark">
                {{ regulationMark }}
              </div>
              <span v-else class="text-grey">N/A</span>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Flavor Text -->
      <v-row v-if="flavorText" class="mt-4">
        <v-col cols="12">
          <v-card variant="outlined">
            <v-card-text class="text-grey-darken-1 font-italic">
              {{ flavorText }}
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script setup>
defineProps({
  weakness: {
    type: Array,
    default: () => []
  },
  resistance: {
    type: Array,
    default: () => []
  },
  retreatCost: {
    type: Array,
    default: () => []
  },
  artist: {
    type: String,
    default: null
  },
  regulationMark: {
    type: String,
    default: null
  },
  flavorText: {
    type: String,
    default: null
  }
})
</script>

<style scoped>
.regulation-mark {
  width: 24px;
  height: 24px;
  border: 1px solid #9e9e9e;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}
</style>