<template>
  <div v-if="hasPrices" class="mb-4">
    <v-expansion-panels v-model="openPanel">
      <!-- Cardmarket ceny -->
      <v-expansion-panel v-if="cardmarket?.prices">
        <v-expansion-panel-title>
          <div class="d-flex align-center">
            <v-img
              src="/images/logos/cardmarket.png"
              alt="Cardmarket"
              width="24"
              height="24"
              class="me-2"
            />
            Cardmarket
            <span class="text-caption text-grey ms-2">
              Aktualizováno {{ formatDate(cardmarket.updatedAt) }}
            </span>
          </div>
        </v-expansion-panel-title>
        <v-expansion-panel-text>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">CM low</div>
              <div class="text-green-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.low_price) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">CM7</div>
              <div class="text-blue-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.avg7) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">CM30</div>
              <div class="text-purple-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.avg30) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">CM trend</div>
              <div class="text-red-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.trend_price) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">RH CM low</div>
              <div class="text-green-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.reverse_holo_low) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">RH CM7</div>
              <div class="text-blue-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.reverse_holo_avg7) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">RH CM30</div>
              <div class="text-purple-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.reverse_holo_avg30) }} €
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">RH CM trend</div>
              <div class="text-red-darken-2 font-weight-bold">
                {{ formatPrice(cardmarket.prices.reverse_holo_trend) }} €
              </div>
            </div>
          </div>
          <v-btn
            v-if="cardmarket.url"
            :href="cardmarket.url"
            target="_blank"
            color="primary"
            variant="text"
            class="mt-4"
            prepend-icon="mdi-open-in-new"
          >
            Zobrazit na Cardmarket
          </v-btn>
        </v-expansion-panel-text>
      </v-expansion-panel>

      <!-- TCGplayer ceny -->
      <v-expansion-panel v-if="tcgplayer?.prices">
        <v-expansion-panel-title>
          <div class="d-flex align-center">
            <v-img
              src="/images/logos/tcgplayer.png"
              alt="TCGplayer"
              width="24"
              height="24"
              class="me-2"
            />
            TCGplayer
            <span class="text-caption text-grey ms-2">
              Aktualizováno {{ formatDate(tcgplayer.updatedAt) }}
            </span>
          </div>
        </v-expansion-panel-title>
        <v-expansion-panel-text>
          <div class="grid grid-cols-4 gap-4">
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">MARKET</div>
              <div class="text-purple-darken-2 font-weight-bold">
                ${{ formatPrice(tcgplayer.prices.price_market) }}
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">LOW</div>
              <div class="text-green-darken-2 font-weight-bold">
                ${{ formatPrice(tcgplayer.prices.price_low) }}
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">MID</div>
              <div class="text-blue-darken-2 font-weight-bold">
                ${{ formatPrice(tcgplayer.prices.price_mid) }}
              </div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-sm text-grey-darken-1">HIGH</div>
              <div class="text-red-darken-2 font-weight-bold">
                ${{ formatPrice(tcgplayer.prices.price_high) }}
              </div>
            </div>
          </div>
          <v-btn
            v-if="tcgplayer.url"
            :href="tcgplayer.url"
            target="_blank"
            color="primary"
            variant="text"
            class="mt-4"
            prepend-icon="mdi-open-in-new"
          >
            Zobrazit na TCGplayer
          </v-btn>
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const openPanel = ref(0) // Panel 0 (Cardmarket) bude defaultně otevřený

const props = defineProps({
  tcgplayer: {
    type: Object,
    default: () => ({
      prices: null,
      url: '',
      updatedAt: null
    })
  },
  cardmarket: {
    type: Object,
    default: () => ({
      prices: null,
      url: '',
      updatedAt: null
    })
  }
})

const hasPrices = computed(() => {
  return (
    (props.tcgplayer?.prices && Object.keys(props.tcgplayer.prices).length > 0) ||
    (props.cardmarket?.prices && Object.keys(props.cardmarket.prices).length > 0)
  )
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('cs-CZ')
}

const formatPrice = (price) => {
    if (typeof price !== 'number' || isNaN(price)) return 'N/A'
    return price.toFixed(2)
  }
</script>

<style scoped>
.prices-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
</style>