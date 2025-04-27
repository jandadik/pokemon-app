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
          <v-row>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">CM low</div>
                <div class="text-subtitle-1 text-green-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.low_price) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">CM7</div>
                <div class="text-subtitle-1 text-blue-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.avg7) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">CM30</div>
                <div class="text-subtitle-1 text-purple-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.avg30) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">CM trend</div>
                <div class="text-subtitle-1 text-red-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.trend_price) }} €
                </div>
              </v-card>
            </v-col>
          </v-row>

          <!-- Cardmarket Reverse Holo ceny -->
          <v-row v-if="hasCmReverseHoloPrices" class="mt-3">
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH CM low</div>
                <div class="text-subtitle-1 text-green-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.reverse_holo_low) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH CM7</div>
                <div class="text-subtitle-1 text-blue-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.reverse_holo_avg7) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH CM30</div>
                <div class="text-subtitle-1 text-purple-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.reverse_holo_avg30) }} €
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH CM trend</div>
                <div class="text-subtitle-1 text-red-darken-2 font-weight-bold">
                  {{ formatPrice(cardmarket.prices.reverse_holo_trend) }} €
                </div>
              </v-card>
            </v-col>
          </v-row>
          
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
          <v-row>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">MARKET</div>
                <div class="text-subtitle-1 text-purple-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_market) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">LOW</div>
                <div class="text-subtitle-1 text-green-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_low) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">MID</div>
                <div class="text-subtitle-1 text-blue-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_mid) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">HIGH</div>
                <div class="text-subtitle-1 text-red-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_high) }}
                </div>
              </v-card>
            </v-col>
          </v-row>
          
          <!-- TCGplayer Reverse Holo ceny -->
          <v-row v-if="hasTcgReverseHoloPrices" class="mt-3">
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH MARKET</div>
                <div class="text-subtitle-1 text-purple-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_reverse_holo_market) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH LOW</div>
                <div class="text-subtitle-1 text-green-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_reverse_holo_low) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH MID</div>
                <div class="text-subtitle-1 text-blue-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_reverse_holo_mid) }}
                </div>
              </v-card>
            </v-col>
            <v-col cols="6" sm="3">
              <v-card variant="outlined" class="pa-3">
                <div class="text-caption text-grey">RH HIGH</div>
                <div class="text-subtitle-1 text-red-darken-2 font-weight-bold">
                  ${{ formatPrice(tcgplayer.prices.price_reverse_holo_high) }}
                </div>
              </v-card>
            </v-col>
          </v-row>
          
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
import { computed, ref, onMounted } from 'vue'

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

onMounted(() => {
  console.log('PricesContainer props received:', props.tcgplayer, props.cardmarket);
})

const hasPrices = computed(() => {
  const hasTcg = props.tcgplayer?.prices && Object.keys(props.tcgplayer.prices).length > 0;
  const hasCm = props.cardmarket?.prices && Object.keys(props.cardmarket.prices).length > 0;
  console.log('Has prices:', { hasTcg, hasCm });
  return hasTcg || hasCm;
})

const hasTcgReverseHoloPrices = computed(() => {
  if (!props.tcgplayer?.prices) return false;
  
  // Kontrola, zda alespoň jedna reverse holo cena má skutečnou hodnotu (není 0, null, undefined)
  return (
    (props.tcgplayer.prices.price_reverse_holo_market && parseFloat(props.tcgplayer.prices.price_reverse_holo_market) > 0) || 
    (props.tcgplayer.prices.price_reverse_holo_low && parseFloat(props.tcgplayer.prices.price_reverse_holo_low) > 0) || 
    (props.tcgplayer.prices.price_reverse_holo_mid && parseFloat(props.tcgplayer.prices.price_reverse_holo_mid) > 0) || 
    (props.tcgplayer.prices.price_reverse_holo_high && parseFloat(props.tcgplayer.prices.price_reverse_holo_high) > 0)
  );
})

const hasCmReverseHoloPrices = computed(() => {
  if (!props.cardmarket?.prices) return false;
  
  // Kontrola, zda alespoň jedna reverse holo cena má skutečnou hodnotu (není 0, null, undefined)
  return (
    (props.cardmarket.prices.reverse_holo_low && parseFloat(props.cardmarket.prices.reverse_holo_low) > 0) || 
    (props.cardmarket.prices.reverse_holo_avg7 && parseFloat(props.cardmarket.prices.reverse_holo_avg7) > 0) || 
    (props.cardmarket.prices.reverse_holo_avg30 && parseFloat(props.cardmarket.prices.reverse_holo_avg30) > 0) || 
    (props.cardmarket.prices.reverse_holo_trend && parseFloat(props.cardmarket.prices.reverse_holo_trend) > 0)
  );
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('cs-CZ')
}

const formatPrice = (price) => {
  // Považovat hodnotu 0 za stejný případ jako null/undefined
  if (price === null || price === undefined || isNaN(parseFloat(price)) || parseFloat(price) === 0) return 'N/A'
  
  // Pokusím se převést hodnotu na číslo
  const numValue = parseFloat(price);
  return numValue.toFixed(2)
}
</script>

<style scoped>
.prices-section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
</style>