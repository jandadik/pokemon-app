<template>
  <AuthenticatedLayout>
    <v-container>
      <!-- Tlačítko zpět -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center mb-4">
            <v-btn
              icon="mdi-arrow-left"
              variant="text"
              :href="route('sets.cards', card.set_id)"
              class="me-4"
            />
          </div>
        </v-col>
      </v-row>

      <v-row v-if="loading">
        <v-col cols="12" class="d-flex justify-center align-center" style="min-height: 50vh">
          <v-progress-circular indeterminate color="primary" />
        </v-col>
      </v-row>

      <v-row v-else-if="card">
        <!-- Levá strana - obrázek -->
        <v-col cols="12" md="4">
          <v-img
            :src="card.img_large"
            :alt="card.name"
            class="rounded-lg"
            cover
          />
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
            v-if="card"
            :card-id="card.id"
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue'
import PokemonAttacks from '@/components/cards/PokemonAttacks.vue'
import PokemonAbilities from '@/components/cards/PokemonAbilities.vue'
import PokemonRules from '@/components/cards/PokemonRules.vue'
import CardAdditionalInfo from '@/components/cards/CardAdditionalInfo.vue'
import PricesContainer from '@/components/cards/PricesContainer.vue'

const props = defineProps({
  card: {
    type: Object,
    required: true
  }
})

const loading = ref(true)
const prices = ref({
  tcgplayer: { prices: null, url: '' },
  cardmarket: { prices: null, url: '' }
})

const typeToImage = (type) => {
  return `/images/energy/${type.toLowerCase()}.png`
}

const fetchPrices = async () => {
  try {
    const [tcgResponse, cardmarketResponse] = await Promise.all([
      fetch(`/api/cards/${props.card.id}/prices/tcg`, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      }),
      fetch(`/api/cards/${props.card.id}/prices/cardmarket`, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
    ])

    const [tcgData, cardmarketData] = await Promise.all([
      tcgResponse.json(),
      cardmarketResponse.json()
    ])

    prices.value = {
      tcgplayer: tcgData.data,
      cardmarket: cardmarketData.data
    }
  } catch (error) {
    console.error('Error fetching prices:', error)
    prices.value = {
      tcgplayer: { prices: null, url: '' },
      cardmarket: { prices: null, url: '' }
    }
  } finally {
    loading.value = false
  }
}

onMounted(fetchPrices)
</script>