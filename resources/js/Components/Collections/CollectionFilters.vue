<template>
  <v-row align="center" dense>
    <v-col cols="12" sm="6" md="4">
      <v-text-field
        v-model="searchFilter"
        :label="$t('collections.filters.search')"
        prepend-inner-icon="mdi-magnify"
        hide-details
        variant="outlined"
        rounded="sm"
        density="compact"
        clearable
      />
    </v-col>
    <v-col cols="6" sm="3" md="2">
      <v-select
        v-model="rarityFilter"
        :items="rarityOptions"
        :label="$t('collections.filters.rarity')"
        variant="outlined"
        rounded="sm"
        density="compact"
        hide-details
        clearable
      />
    </v-col>
    <v-col cols="6" sm="3" md="2">
      <v-select
        v-model="languageFilter"
        :items="languageOptions"
        :label="$t('collections.form.language')"
        variant="outlined"
        rounded="sm"
        density="compact"
        hide-details
        clearable
      />
    </v-col>
    <v-col cols="6" sm="3" md="2">
      <v-select
        v-model="conditionFilter"
        :items="conditionOptions"
        :label="$t('collections.cards.table.condition')"
        variant="outlined"
        rounded="sm"
        density="compact"
        hide-details
        clearable
      />
    </v-col>
    <v-col cols="6" sm="3" md="2">
      <v-select
        v-model="sortByFilter"
        :items="sortOptions"
        :label="$t('collections.filters.sort')"
        variant="outlined"
        rounded="sm"
        density="compact"
        hide-details
        clearable
      />
    </v-col>
  </v-row>
</template>

<script setup>
import { computed, getCurrentInstance } from 'vue';
import { useCollectionsStore } from '@/stores/collections';

const props = defineProps({
  filterOptions: {
    type: Object,
    required: true
  }
});

const collectionsStore = useCollectionsStore();

// Computed properties pro přímou práci se store
const searchFilter = computed({
  get: () => collectionsStore.filters.search || '',
  set: (value) => collectionsStore.updateFilter('search', value)
});

const rarityFilter = computed({
  get: () => collectionsStore.filters.rarity || '',
  set: (value) => collectionsStore.updateFilter('rarity', value)
});

const languageFilter = computed({
  get: () => collectionsStore.filters.language || '',
  set: (value) => collectionsStore.updateFilter('language', value)
});

const conditionFilter = computed({
  get: () => collectionsStore.filters.condition || '',
  set: (value) => collectionsStore.updateFilter('condition', value)
});

const sortByFilter = computed({
  get: () => collectionsStore.filters.sort_by || 'name',
  set: (value) => collectionsStore.updateFilter('sort_by', value)
});

const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

const rarityOptions = computed(() => [
  { title: $t('collections.filters.all_rarities') || 'Všechny', value: '' },
  ...((props.filterOptions.rarities || []).map(r => ({ title: r, value: r })))
]);
const languageOptions = computed(() => [
  { title: $t('collections.filters.all_languages') || 'Všechny', value: '' },
  ...((props.filterOptions.languages || []).map(l => ({ title: l, value: l })))
]);
const conditionOptions = computed(() => [
  { title: $t('collections.filters.all_conditions') || 'Všechny', value: '' },
  ...((props.filterOptions.conditions || []).map(c => ({ title: c, value: c })))
]);
const sortOptions = [
  { title: $t('collections.filters.sort_name') || 'Název', value: 'name' },
  { title: $t('collections.filters.sort_number') || 'Číslo', value: 'number' },
  { title: $t('collections.filters.sort_rarity') || 'Rarita', value: 'rarity' },
  { title: $t('collections.filters.sort_price') || 'Cena', value: 'price' },
  { title: $t('collections.filters.sort_created_at') || 'Datum přidání', value: 'created_at' },
];
</script> 