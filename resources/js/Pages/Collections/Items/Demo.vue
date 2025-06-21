<template>

    <Head title="Demo: Vyhledání karty a výběr varianty" />
    
    <v-container>
      <!-- Hlavička stránky -->
      <v-card class="mb-4 page-header">
        <v-card-text>
          <v-row align="center">
            <v-col cols="12" md="2" class="d-flex justify-center align-center">
              <div class="page-logo-container">
                <v-icon
                  size="64"
                  color="primary"
                  icon="mdi-test-tube"
                  class="mx-auto"
                />
              </div>
            </v-col>
            <v-col cols="12" md="10">
              <h1 class="text-h4">Demo: Vyhledání karty a výběr varianty</h1>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
            
            <!-- Krok 1: Vyhledání karty (úkol 2.3) -->
            <v-card class="mb-6">
              <v-card-title>
                <v-icon start>mdi-magnify</v-icon>
                Krok 1: Vyhledat kartu
              </v-card-title>
              <v-card-text>
                <QuickCardSearch @card-selected="onCardSelected" />
                
                <div v-if="selectedCard" class="mt-4">
                  <v-alert type="success" variant="tonal">
                    <strong>Vybraná karta:</strong> {{ selectedCard.name }}
                    <br>
                    <strong>Set:</strong> {{ selectedCard.set_name }} - {{ selectedCard.number }}
                    <br>
                    <strong>Card ID:</strong> {{ selectedCard.card_id }}
                  </v-alert>
                </div>
              </v-card-text>
            </v-card>

            <!-- Krok 2: Výběr varianty (úkol 2.4) -->
            <v-card v-if="selectedCard" class="mb-6">
              <v-card-title>
                <v-icon start>mdi-card-multiple</v-icon>
                Krok 2: Vybrat variantu karty
              </v-card-title>
              <v-card-text>
                <CardVariantSelector 
                  :card-id="selectedCard.card_id"
                  :selected-card="selectedCard"
                  :selected-variant-type="selectedVariantType"
                  @variant-selected="onVariantSelected"
                />
                
                <div v-if="selectedVariant" class="mt-4">
                  <v-alert type="success" variant="tonal">
                    <strong>Vybraná varianta:</strong> {{ selectedVariant.name }}
                    <br>
                    <strong>Kód:</strong> {{ selectedVariant.code }}
                    <br>
                    <strong>Variant ID:</strong> {{ selectedVariant.variant }}
                    <br>
                    <strong>CM ID:</strong> {{ selectedVariant.cm_id || 'N/A' }}
                    <br>
                    <span v-if="selectedVariant.description">
                      <strong>Popis:</strong> {{ selectedVariant.description }}
                      <br>
                    </span>
                    <span v-if="selectedVariant.prices">
                      <strong>Ceny (CardMarket):</strong> 
                      Trend: {{ selectedVariant.prices.cardmarket?.trend || 'N/A' }}€, 
                      Avg30: {{ selectedVariant.prices.cardmarket?.avg30 || 'N/A' }}€
                    </span>
                  </v-alert>
                </div>
              </v-card-text>
            </v-card>

            <!-- Výsledek integrace -->
            <v-card v-if="selectedCard && selectedVariant" class="mb-6">
              <v-card-title>
                <v-icon start>mdi-check-circle</v-icon>
                Výsledek integrace úkolů 2.3 + 2.4
              </v-card-title>
              <v-card-text>
                <v-alert type="info" variant="tonal">
                  <div class="text-body-1">
                    <strong>Připraveno pro úkol 2.5 (formulář):</strong>
                  </div>
                  <ul class="mt-2 ml-4">
                    <li><strong>Karta:</strong> {{ selectedCard.name }} ({{ selectedCard.card_id }})</li>
                    <li><strong>Varianta:</strong> {{ selectedVariant.name }} ({{ selectedVariant.code }})</li>
                    <li><strong>Set:</strong> {{ selectedCard.set_name }} - {{ selectedCard.number }}</li>
                  </ul>
                  <div class="mt-3 text-caption text-medium-emphasis">
                    Tyto údaje budou předány formuláři pro vytvoření položky sbírky v úkolu 2.5
                  </div>
                </v-alert>
              </v-card-text>
            </v-card>

            <!-- Reset -->
            <div class="text-center">
              <v-btn 
                color="secondary" 
                variant="outlined"
                @click="resetDemo"
                prepend-icon="mdi-refresh"
              >
                Resetovat demo
              </v-btn>
            </div>

    </v-container>

</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import QuickCardSearch from '@/Components/Card/QuickCardSearch.vue';
import CardVariantSelector from '@/Components/Card/CardVariantSelector.vue';

// Demo data
const selectedCard = ref(null);
const selectedVariant = ref(null);
const selectedVariantType = ref(null);

function onCardSelected(card) {
  console.log('Karta vybrána:', card);
  selectedCard.value = card;
  // Reset varianty při výběru nové karty
  selectedVariant.value = null;
  selectedVariantType.value = null;
}

function onVariantSelected(variant) {
  console.log('Varianta vybrána:', variant);
  selectedVariant.value = variant;
  selectedVariantType.value = String(variant.code);
}

function resetDemo() {
  selectedCard.value = null;
  selectedVariant.value = null;
  selectedVariantType.value = null;
}
</script> 