<template>
  <v-card>
    <v-card-title>{{ $t('collections.form.title.' + mode) }}</v-card-title>
    <v-card-text>
      <!-- Náhled vybrané karty a varianty - jen v edit režimu -->
      <div v-if="mode === 'edit'" class="mb-4">
        <div v-if="card && variant">
          <div class="d-flex align-center mb-2">
            <img 
              :src="getCardImageUrl(card)" 
              :alt="card.name"
              height="60" 
              class="mr-3 rounded elevation-1" 
              @error="handleImageError"
            />
            <div>
              <div class="font-weight-bold">{{ card.name }}</div>
              <div class="text-caption" v-if="variant.name && variant.name !== 'N/A'">{{ variant.name }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sekce: Detaily položky -->
      <v-row>
        <v-col cols="12" md="6">
          <ConditionSelect v-model="form.condition" :error-messages="form.errors.condition" />
        </v-col>
        <v-col cols="12" md="6">
          <LanguageSelect v-model="form.language" :error-messages="form.errors.language" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <QuantityInput v-model="form.quantity" :error-messages="form.errors.quantity" />
        </v-col>
        <v-col cols="12" md="4">
          <PriceInput v-model="form.purchase_price" :error-messages="form.errors.purchase_price" />
        </v-col>
        <v-col cols="12" md="4">
          <LocationInput v-model="form.location" :error-messages="form.errors.location" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="6">
          <GradingSection 
            v-model:grading="form.grading" 
            v-model:gradingCert="form.grading_cert" 
            :error-messages="form.errors.grading || form.errors.grading_cert" 
          />
        </v-col>
        <v-col cols="12" md="6">
          <FirstEditionCheckbox v-model="form.first_edition" :error-messages="form.errors.first_edition" />
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <NoteInput v-model="form.note" :error-messages="form.errors.note" />
        </v-col>
      </v-row>
       <v-alert
          v-if="form.hasErrors && !Object.values(form.errors).some(err => err !== '')"
          type="error"
          variant="tonal"
          density="compact"
          class="mt-4"
        >
          {{ $t('ui.messages.validation_errors_generic') }} 
        </v-alert>
    </v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn color="primary" @click="$emit('submit')" :loading="form.processing" :disabled="form.processing">{{ $t('collections.form.save') }}</v-btn>
      <v-btn color="secondary" @click="$emit('cancel')" :disabled="form.processing">{{ $t('collections.form.cancel') }}</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, reactive, watch, toRefs } from 'vue';
// Import card utilities pro správné zobrazení obrázků
import { getCardImageUrl, handleImageError } from '@/composables/useCardUtils';
// Import podkomponenty (zatím placeholdery)
import ConditionSelect from './ConditionSelect.vue';
import LanguageSelect from './LanguageSelect.vue';
import QuantityInput from './QuantityInput.vue';
import PriceInput from './PriceInput.vue';
import GradingSection from './GradingSection.vue';
import FirstEditionCheckbox from './FirstEditionCheckbox.vue';
import LocationInput from './LocationInput.vue';
import NoteInput from './NoteInput.vue';

const props = defineProps({
  card: { type: Object, required: true },
  variant: { type: Object, required: true },
  mode: { type: String, default: 'create' },
  form: { type: Object, required: true }
});

const emit = defineEmits(['submit', 'cancel']);
</script> 