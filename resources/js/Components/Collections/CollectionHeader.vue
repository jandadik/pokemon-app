<template>
    <!-- Hlavička - řádek 1: ikona, název, chips, akce -->
    <div class="mb-1">
        <v-row align="center" dense no-gutters>
            <v-col cols="auto" class="me-2">
                <v-avatar size="24" color="primary" class="elevation-1">
                    <v-icon size="14" color="white">mdi-folder-multiple-image</v-icon>
                </v-avatar>
            </v-col>
            <v-col cols="auto" class="me-3">
                <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
                    {{ collection ? collection.name : $t('collections.titles.detail') }}
                </h1>
            </v-col>
            <v-col cols="auto" class="me-1" v-if="collection">
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon 
                            v-bind="props"
                            :icon="collection.is_public ? 'mdi-earth' : 'mdi-lock'" 
                            :color="collection.is_public ? 'success' : 'warning'"
                            size="18"
                        />
                    </template>
                    <span>{{ collection.is_public ? 'Veřejná sbírka' : 'Soukromá sbírka' }}</span>
                </v-tooltip>
            </v-col>
            <v-col cols="auto" class="me-2" v-if="collection && collection.is_default">
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon 
                            v-bind="props"
                            icon="mdi-star" 
                            color="amber"
                            size="18"
                        />
                    </template>
                    <span>Výchozí sbírka</span>
                </v-tooltip>
            </v-col>
            <v-col cols="auto" class="me-2" v-if="collection && stats">
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-btn
                            v-bind="props"
                            :icon="showStats ? 'mdi-chart-box' : 'mdi-chart-box-outline'"
                            :color="showStats ? 'primary' : 'default'"
                            variant="text"
                            size="small"
                            @click="$emit('toggle-stats')"
                        />
                    </template>
                    <span>{{ showStats ? 'Skrýt statistiky' : 'Zobrazit statistiky' }}</span>
                </v-tooltip>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto">
                <div class="d-flex gap-1">
                    <!-- Primární akce: Přidat -->
                    <v-btn
                        v-if="can && can.update" 
                        icon="mdi-plus"
                        color="success"
                        variant="tonal"
                        size="small"
                        @click="$emit('add-item')"
                    >
                        <v-icon>mdi-plus</v-icon>
                        <v-tooltip activator="parent" location="bottom">
                            {{ $t('collections.items.add_new_item') }}
                        </v-tooltip>
                    </v-btn>

                    <!-- Menu s ostatními akcemi -->
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                icon="mdi-dots-vertical"
                                variant="tonal"
                                size="small"
                                v-bind="props"
                            >
                                <v-icon>mdi-dots-vertical</v-icon>
                                <v-tooltip activator="parent" location="bottom">
                                    Další akce
                                </v-tooltip>
                            </v-btn>
                        </template>
                        <v-list density="compact" min-width="200">
                            <v-list-item 
                                v-if="can && can.edit" 
                                prepend-icon="mdi-pencil"
                                @click="$emit('edit-collection')"
                            >
                                <v-list-item-title>{{ $t('collections.buttons.edit') }}</v-list-item-title>
                            </v-list-item>

                            <v-list-item
                                v-if="can && can.toggleDefault && !collection.is_default"
                                prepend-icon="mdi-star-outline"
                                @click="$emit('set-default')"
                                :disabled="isSettingDefault"
                            >
                                <v-list-item-title>{{ $t('collections.buttons.set_default') }}</v-list-item-title>
                            </v-list-item>

                            <v-list-item
                                v-if="can && can.toggleVisibility"
                                :prepend-icon="collection.is_public ? 'mdi-lock' : 'mdi-earth'"
                                @click="$emit('toggle-visibility')"
                                :disabled="isTogglingVisibility"
                            >
                                <v-list-item-title>
                                    {{ collection.is_public ? $t('collections.buttons.make_private') : $t('collections.buttons.make_public') }}
                                </v-list-item-title>
                            </v-list-item>

                            <v-divider class="my-1"></v-divider>

                            <v-list-item 
                                v-if="can && can.delete" 
                                prepend-icon="mdi-delete"
                                class="text-error"
                                @click="$emit('delete-collection')"
                            >
                                <v-list-item-title>{{ $t('collections.buttons.delete') }}</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue';

const instance = getCurrentInstance();
const $t = instance.appContext.config.globalProperties.$t;

defineProps({
    collection: Object,
    can: Object,
    stats: Object,
    showStats: Boolean,
    isSettingDefault: Boolean,
    isTogglingVisibility: Boolean,
});

defineEmits([
    'toggle-stats',
    'add-item', 
    'edit-collection',
    'set-default',
    'toggle-visibility',
    'delete-collection'
]);
</script> 