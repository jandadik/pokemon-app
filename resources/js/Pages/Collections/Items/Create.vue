<template>
  <v-container>
    <Head :title="$t('collections.items.create_title')" />

    <!-- Kompaktní hlavička -->
    <div class="mb-4">
      <v-row align="center" dense no-gutters>
        <v-col cols="auto" class="me-2">
          <v-avatar size="24" color="primary" class="elevation-1">
            <v-icon size="14" color="white">mdi-card-plus-outline</v-icon>
          </v-avatar>
        </v-col>
        <v-col cols="auto" class="me-3">
          <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
            {{ $t('collections.items.create_title') }}
          </h1>
        </v-col>
        <v-spacer></v-spacer>
      </v-row>
    </div>

    <!-- Výběr karty -->
    <v-card class="mb-4 elevation-1" v-if="!selectedCard">
      <v-card-title class="text-h6 d-flex align-center">
        <v-icon start>mdi-magnify</v-icon>
        {{ $t('collections.items.select_card') }}
      </v-card-title>
      <v-card-text>
        <p class="text-sm text-gray-600 mb-4">{{ $t('collections.items.card_search_instructions') }}</p>
        <QuickCardSearch 
          @card-selected="onCardSelected"
        />
      </v-card-text>
    </v-card>

    <!-- Výběr varianty -->
    <div v-if="selectedCard && !selectedVariant" class="mb-4">
      <CardVariantSelector
        :card-id="selectedCard.card_id"
        :selected-card="selectedCard"
        @variant-selected="onVariantSelected"
      />
    </div>

    <!-- Náhled vybrané karty a varianty -->
    <v-card v-if="selectedCard && selectedVariant" class="mb-4 elevation-1">
      <v-card-title class="text-h6 d-flex align-center">
        <v-icon start>mdi-check-circle</v-icon>
        {{ $t('collections.items.selected_card_variant') }}
      </v-card-title>
      <v-card-text>
        <div class="d-flex align-center">
          <v-img
            v-if="selectedVariant.image_url"
            :src="selectedVariant.image_url"
            :alt="selectedCard.name"
            height="100"
            width="72"
            class="mr-4 elevation-1 rounded flex-shrink-0"
          />
          <div class="flex-grow-1">
            <div class="text-h6 mb-1">{{ selectedCard.name }}</div>
            <div class="text-subtitle-1 text-medium-emphasis mb-2">
              {{ selectedVariant.name || $t('collections.variant_selection.default_variant') }}
            </div>
            <div class="text-caption text-medium-emphasis">
              {{ selectedCard.set_name }} - {{ selectedCard.number }}
            </div>
          </div>
          <v-btn
            icon="mdi-close"
            size="small"
            variant="text"
            @click="resetSelection"
            :title="$t('collections.items.change_selection')"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- Formulář pro detaily položky -->
    <CollectionItemForm
      v-if="selectedCard && selectedVariant" 
      :card="selectedCard"
      :variant="selectedVariant"
      :form="form"
      mode="create"
      @submit="submitForm"
      @cancel="handleCancel"
    />

    <!-- Chybové hlášení -->
    <v-alert
      v-if="error"
      type="error"
      variant="tonal"
      class="mb-4"
    >
      {{ error }}
    </v-alert>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
// import AppLayout from '@/Layouts/AppLayout.vue';
import CollectionItemForm from '@/Components/Collections/CollectionItem/CollectionItemForm.vue';
import QuickCardSearch from '@/Components/Card/QuickCardSearch.vue';
import CardVariantSelector from '@/Components/Card/CardVariantSelector.vue';

const props = defineProps({
  collection: { type: Object, required: true },
});

// Stav výběru karty a varianty
const selectedCard = ref(null);
const selectedVariant = ref(null);
const error = ref(null);

// Formulář pro položku
const form = useForm({
  // Karta a varianta (podle výběru uživatele)
  card_id: '', // ID karty z výběru
  variant_id: '', // ID varianty z výběru
  variant_type: '', // Typ varianty z výběru

  // Uživatelem zadané údaje
  condition: 'near_mint', // Předvolba: Near Mint
  language: 'english', // Předvolba: Anglicky
  quantity: 1,
  purchase_price: '',
  grading: '',
  grading_cert: '',
  first_edition: false,
  location: '',
  note: ''
});

// Handler pro výběr karty
const onCardSelected = (card) => {
  console.log('Vybraná karta:', card);
  selectedCard.value = card;
  selectedVariant.value = null; // Reset varianty při změně karty
  error.value = null;
};

// Handler pro výběr varianty
const onVariantSelected = (variant) => {
  console.log('Vybraná varianta:', variant);
  selectedVariant.value = variant;
  error.value = null;
};

// Reset výběru
const resetSelection = () => {
  selectedCard.value = null;
  selectedVariant.value = null;
  error.value = null;
};

// Odeslání formuláře
const submitForm = () => {
  if (!selectedCard.value || !selectedVariant.value) {
    error.value = 'Musíte vybrat kartu a její variantu.';
    return;
  }

  // Příprava dat pro odeslání
  const dataToSubmit = {
    ...form.data(),
    card_id: selectedCard.value.card_id,
    variant_id: selectedVariant.value.cm_id || selectedVariant.value.id,
    variant_type: String(selectedVariant.value.code || selectedVariant.value.variant_type)
  };

  console.log('Odesílám data:', dataToSubmit);

  form.transform(() => dataToSubmit)
    .post(route('collections.items.store', { collection: props.collection.id }), {
      onSuccess: () => {
        // Přesměrování na detail kolekce (show stránku)
        router.visit(route('collections.show', { collection: props.collection.id }));
      },
      onError: (errors) => {
        console.error('Chyba při ukládání:', errors);
        if (errors.message) {
          error.value = errors.message;
        } else {
          error.value = 'Nastala chyba při ukládání položky. Zkuste to prosím znovu.';
        }
      }
    });
};

// Zrušení
const handleCancel = () => {
  router.visit(route('collections.show', { collection: props.collection.id }));
};

// Potřebné lokalizační klíče:
// collections.items.create_title
// collections.items.create_description
// collections.items.select_card_variant
// collections.items.demo_selection_notice
// ui.messages.validation_errors_generic (pokud ještě neexistuje)
</script>

 