<template>
    <v-card height="350">
        <div class="pa-2">
            <v-img
                :src="set.logo_url"
                :alt="set.name"
                height="200"
                class="d-flex align-center justify-center"
                :style="{ cursor: 'pointer' }"
                contain
                @click="showCards"
            />
        </div>
        
        <v-card-title class="text-truncate">
            {{ set.name }}
            <span class="text-caption text-grey ms-2" v-if="set.ptcgo_code">
                ({{ set.ptcgo_code }})
            </span>
        </v-card-title>
        
        <v-card-subtitle class="text-truncate">
            {{ set.series }}
        </v-card-subtitle>
        
        <v-card-text>
            <div class="d-flex justify-space-between align-center">
                <div class="d-flex flex-column">
                    <div class="text-truncate">
                        Počet karet: {{ set.printed_total }} / {{ set.total }}
                    </div>
                    <div class="text-truncate text-caption text-grey">
                        {{ formatDate(set.release_date) }}
                    </div>
                </div>
                <div>
                    <v-btn
                        prepend-icon="mdi-chart-box"
                        color="primary"
                        @click="showStats"
                    >
                        Detaily
                        <v-tooltip
                            activator="parent"
                            location="top"
                        >
                            Statistiky setu
                        </v-tooltip>
                    </v-btn>
                </div>
            </div>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    set: {
        type: Object,
        required: true
    }
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('cs-CZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Pro budoucí implementaci - přesměrování na stránku s kartami
const showCards = () => {
    // Prozatím jen vypíšeme do konzole
    console.log('Show cards for set:', props.set.id)
    // router.visit(route('sets.cards', props.set.id)) // Zakomentováno do implementace routy
}

// Pro budoucí implementaci - zobrazení statistik
const showStats = () => {
    // Zde bude logika pro zobrazení statistik (modal nebo přesměrování)
    console.log('Show stats for set:', props.set.id)
}
</script>