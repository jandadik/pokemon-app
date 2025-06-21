<template>
  <v-container>
    <Head :title="$t('collections.items.edit_title')" />

    <!-- Kompaktní hlavička -->
    <div class="mb-4">
      <v-row align="center" dense no-gutters>
        <v-col cols="auto" class="me-2">
          <v-avatar size="24" color="primary" class="elevation-1">
            <v-icon size="14" color="white">mdi-file-document-edit-outline</v-icon>
          </v-avatar>
        </v-col>
        <v-col cols="auto" class="me-3">
          <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
            {{ $t('collections.items.edit_title') }}
          </h1>
        </v-col>
        <v-spacer></v-spacer>
      </v-row>
    </div>
    
    <CollectionItemForm
      v-if="form.card_name" 
      :card="{ 
        id: form.card_id, 
        name: form.card_name,
        img_file_small: cardData.img_file_small,
        img_small: cardData.img_small,
        img_file_large: cardData.img_file_large,
        img_large: cardData.img_large,
        set_id: cardData.set_id,
        number: cardData.number
      }"       
      :variant="{ 
        id: form.variant_id, 
        name: form.variant_name
      }"   
      :form="form"
      mode="edit"
      @submit="submitForm"
      @cancel="handleCancel"
    />
  </v-container>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import CollectionItemForm from '@/Components/Collections/CollectionItem/CollectionItemForm.vue';

const props = defineProps({
  collectionId: { type: [String, Number], required: true }, 
  itemId: { type: [String, Number], required: true },       
  item: { type: Object, required: true }, // Data z controlleru: item.condition, item.language, ..., item.card.id, item.card.name, item.variant.id, item.variant.name, item.variant.image_url
  cardData: { type: Object, required: true } // Celá data karty pro obrázky
});

const form = useForm({
  // Data z props.item pro inicializaci formuláře (controller posílá ploché pole)
  card_id: props.item.card_id || null, // Pro zobrazení
  card_name: props.item.card_name || 'N/A', // Pro zobrazení
  variant_id: props.item.variant_id || null, // Pro zobrazení
  variant_name: props.item.variant_name || 'N/A', // Pro zobrazení
  image_url: props.item.image_url || null, // Pro zobrazení

  condition: props.item.condition || '',
  language: props.item.language || '',
  quantity: props.item.quantity || 1,
  purchase_price: props.item.purchase_price || '',
  grading: props.item.grading || '',
  grading_cert: props.item.grading_cert || '',
  first_edition: props.item.first_edition || false,
  location: props.item.location || '',
  note: props.item.note || ''
});

const submitForm = () => {
  // card_id, variant_id a info pro zobrazení se neposílají v PUT requestu, ty se nemění
  const { card_id, card_name, variant_id, variant_name, image_url, ...payload } = form.data();

  form.transform(data => payload) // Odesíláme jen data, která se mohou měnit
      .put(route('collections.items.update', { collection: props.collectionId, item: props.itemId }), {
    onSuccess: () => {
      router.visit(route('collections.show', { collection: props.collectionId }));
    },
    onError: (errors) => {
      console.error('Chyba při aktualizaci:', errors);
    }
  });
};

function handleCancel() {
  router.visit(route('collections.show', { collection: props.collectionId }));
}

// Potřebné lokalizační klíče:
// collections.items.edit_title
// collections.items.edit_description
// collections.items.editing_item_info
// collections.items.no_item_data_for_edit
</script>

 