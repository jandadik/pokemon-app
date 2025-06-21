<template>
  <v-card v-if="cardId" class="card-variant-selector">
    <v-card-title>
      <v-icon start>mdi-card-multiple</v-icon>
      {{ $t('collections.variant_selection.title') }}
    </v-card-title>
    
    <v-card-text>
      <!-- Zobrazení vybrané karty -->
      <div v-if="selectedCard" class="selected-card-info mb-4">
        <v-alert
          type="info"
          variant="tonal"
          density="compact"
          class="mb-3"
        >
          <template #prepend>
            <v-icon>mdi-information</v-icon>
          </template>
          {{ $t('collections.variant_selection.selected_card') }}: <strong>{{ selectedCard.name }}</strong>
          <span v-if="selectedCard.set_name"> ({{ selectedCard.set_name }} - {{ selectedCard.number }})</span>
        </v-alert>
      </div>

      <!-- Loading state -->
      <v-skeleton-loader 
        v-if="loading" 
        type="list-item@3"
        class="mb-4"
      />
      
      <!-- Seznam dostupných variant -->
      <div v-else-if="variantTypes.length > 0">
        <v-list-subheader class="px-0 mb-2">
          {{ $t('collections.variant_selection.select_variant') }}
        </v-list-subheader>
        
        <v-list density="compact" class="variant-list">
          <VariantTypeItem
            v-for="variantType in variantTypes"
            :key="variantType.code"
            :variant-type="variantType"
            :is-selected="String(selectedVariantType) === String(variantType.code)"
            :disabled="disabled"
            @click="selectVariant(variantType)"
          />
        </v-list>
      </div>
      
      <!-- Žádné varianty -->
      <v-alert 
        v-else-if="!loading && cardId"
        type="info" 
        variant="tonal"
        density="compact"
      >
        <template #prepend>
          <v-icon>mdi-information-outline</v-icon>
        </template>
        {{ $t('collections.variant_selection.no_variants') }}
      </v-alert>

      <!-- Error state -->
      <v-alert
        v-if="error"
        type="error"
        variant="tonal"
        density="compact"
        class="mt-3"
      >
        <template #prepend>
          <v-icon>mdi-alert-circle</v-icon>
        </template>
        {{ error }}
      </v-alert>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import VariantTypeItem from './VariantTypeItem.vue';

const props = defineProps({
  cardId: {
    type: String,
    default: null
  },
  selectedVariantType: {
    type: String,
    default: null
  },
  disabled: {
    type: Boolean,
    default: false
  },
  selectedCard: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['variant-selected']);

const variantTypes = ref([]);
const loading = ref(false);
const error = ref(null);

// Načtení variant při změně cardId
watch(() => props.cardId, (newCardId) => {
  if (newCardId) {
    loadVariantTypes();
  } else {
    variantTypes.value = [];
    error.value = null;
  }
}, { immediate: true });

async function loadVariantTypes() {
  if (!props.cardId || props.disabled) return;
  
  loading.value = true;
  error.value = null;
  
  try {
    // Použití nového endpointu nezávislého na kolekci
    const url = route('catalog.cards.variants', {
      card: props.cardId
    });
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    if (!response.ok) {
      const errorText = await response.text();
      console.error('Error response:', errorText);
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    variantTypes.value = data || [];
    
  } catch (err) {
    console.error('Chyba při načítání variant:', err);
    error.value = 'Nepodařilo se načíst dostupné varianty karty.';
    variantTypes.value = [];
  } finally {
    loading.value = false;
  }
}

async function selectVariant(variantType) {
  if (props.disabled) return;
  
  try {
    // Zavolat endpoint pro získání kompletních informací o variantě
    const response = await fetch(
      route('catalog.cards.variants.details', {
        card: props.cardId,
        variantTypeCode: variantType.code
      }),
      {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    );
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const variantDetails = await response.json();
    
    emit('variant-selected', {
      // Základní informace o typu
      code: variantType.code,
      variant: variantType.variant,
      name: variantType.name,
      description: variantType.description || null,
      
      // Kompletní informace o variantě
      ...variantDetails
    });
  } catch (err) {
    console.error('Chyba při získávání informací o variantě:', err);
    // Fallback - pošleme alespoň základní informace
    emit('variant-selected', {
      code: variantType.code,
      variant: variantType.variant,
      name: variantType.name,
      description: variantType.description || null
    });
  }
}
</script>

<style scoped>
.card-variant-selector {
  border: 1px solid rgb(var(--v-theme-surface-variant));
}

.selected-card-info {
  border-radius: 4px;
}

.variant-list {
  border: 1px solid rgb(var(--v-theme-outline));
  border-radius: 4px;
}

.variant-list .v-list-item {
  border-bottom: 1px solid rgb(var(--v-theme-surface-variant));
}

.variant-list .v-list-item:last-child {
  border-bottom: none;
}
</style> 