<template>
  
    <v-container>
      <!-- Tlačítko zpět -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center mb-4">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              @click="goBack"
              class="me-4"
            />
          </div>
        </v-col>
      </v-row>

      <v-row v-if="card">
        <!-- Levá strana - obrázek -->
        <v-col cols="12" md="4">
          <div class="card-image-container">
            <img
              :src="card.img_large"
              :alt="card.name"
              class="card-detail-image rounded-lg"
              @error="handleImageError"
              loading="eager"
            />
          </div>
        </v-col>

        <!-- Pravá strana - detaily -->
        <v-col cols="12" md="8">
          <div class="d-flex justify-space-between align-start mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold">{{ card.name }}</h1>
              <div class="text-h6 text-grey">
                {{ card.supertype }}{{ card.subtypes?.length ? ` - ${card.subtypes.join(', ')}` : '' }}
              </div>
            </div>
            <div v-if="card.supertype === 'Pokémon'" class="d-flex align-center">
              <span class="me-2 font-weight-bold">HP {{ card.hp }}</span>
              <v-img
                v-for="type in card.types"
                :key="type"
                :src="typeToImage(type)"
                :alt="type"
                width="24"
                height="24"
              />
            </div>
          </div>

          <PricesContainer
            :tcgplayer="prices.tcgplayer"
            :cardmarket="prices.cardmarket"
          />

          <PokemonAbilities
            v-if="card?.abilities"
            :abilities="card.abilities"
          />

          <PokemonAttacks
            v-if="card.attacks?.length"
            :attacks="card.attacks"
          />

          <PokemonRules
            v-if="card?.rules"
            :rules="card.rules"
          />

          <CardAdditionalInfo
            :weakness="card.weaknesses"
            :resistance="card.resistances"
            :retreat-cost="card.retreat_cost"
            :artist="card.illustrator"
            :rarity="card.rarity"
            :number="card.number"
            :printed-total="card.printed_total"
            :set-id="card.set_id"
            :set-name="card.set_name"
            :set-symbol-url="card.set_symbol_url"
            :regulation-mark="card.regulation_mark"
            :flavor-text="card.flavor_text"
            :legalities="card.legalities || {}"
          />
        </v-col>
      </v-row>

      <v-row v-else>
        <v-col cols="12" class="text-center py-12">
          <p class="text-h5 text-grey">Karta nebyla nalezena</p>
        </v-col>
      </v-row>
    </v-container>
  
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PokemonAttacks from '@/Components/Card/PokemonAttacks.vue'
import PokemonAbilities from '@/Components/Card/PokemonAbilities.vue'
import PokemonRules from '@/Components/Card/PokemonRules.vue'
import CardAdditionalInfo from '@/Components/Card/CardAdditionalInfo.vue'
import PricesContainer from '@/Components/Card/PricesContainer.vue'
import { handleImageError } from '@/composables/useCardUtils'

const props = defineProps({
  card: {
    type: Object,
    required: true
  },
  referrer: {
    type: String,
    default: null
  }
})

// Debug výpis
console.log('Karta v Show.vue:', props.card)

const loading = ref(false)

// Připravíme ceny z dat karty místo načítání přes API
const prices = computed(() => {
  const result = {
    tcgplayer: { 
      prices: {
        price_market: props.card.price_tcg_market,
        price_low: props.card.price_tcg_low,
        price_mid: props.card.price_tcg_mid,
        price_high: props.card.price_tcg_high,
        price_direct_low: props.card.price_tcg_direct_low,
        price_reverse_holo_market: props.card.price_tcg_reverse_holo_market,
        price_reverse_holo_low: props.card.price_tcg_reverse_holo_low,
        price_reverse_holo_mid: props.card.price_tcg_reverse_holo_mid,
        price_reverse_holo_high: props.card.price_tcg_reverse_holo_high
      }, 
      url: props.card.url_tcgplayer || '',
      updatedAt: props.card.price_tcg_updated_at || null
    },
    cardmarket: { 
      prices: {
        avg30: props.card.price_cm_avg30,
        avg7: props.card.price_cm_avg7,
        trend_price: props.card.price_cm_trend,
        low_price: props.card.price_cm_low,
        reverse_holo_avg30: props.card.price_cm_reverse_holo_avg30,
        reverse_holo_avg7: props.card.price_cm_reverse_holo_avg7,
        reverse_holo_low: props.card.price_cm_reverse_holo_low,
        reverse_holo_trend: props.card.price_cm_reverse_holo_trend,
        reverse_holo_sell: props.card.price_cm_reverse_holo_sell,
        reverse_holo_avg1: props.card.price_cm_reverse_holo_avg1
      }, 
      url: props.card.url_cardmarket || '',
      updatedAt: props.card.price_cm_updated_at || null
    }
  };
  
  console.log('Zpracované ceny v Show.vue:', result);
  return result;
})

const typeToImage = (type) => {
  return `/images/energy/${type.toLowerCase()}.png`
}

const goBack = () => {
  // Pokud máme referrer v props, použijeme ho pro návrat
  if (props.referrer) {
    router.visit(props.referrer);
  } else {
    // Pokud nemáme referrer, vrátíme se na stránku setů jako fallback
    router.visit(`/sets/${props.card.set_id}/cards`);
  }
}
</script>

<style scoped>
.card-image-container {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
}

.card-detail-image {
  width: 100%;
  height: auto;
  display: block;
  transition: transform 0.3s ease;
}

.card-detail-image:hover {
  transform: scale(1.02);
}
</style>