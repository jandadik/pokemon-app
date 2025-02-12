<template>
    <v-card>
        <div class="position-relative">
            <v-img
                :src="card.img_small"
                :alt="card.name"
                contain
                width="100%"                
                :style="{ cursor: 'pointer' }"
                @click="showDetail"
            />
            <div class="card-actions">
                <v-btn
                    icon="mdi-cards"
                    color="primary"
                    size="x-small"
                    variant="text"
                    style="background-color: white;"
                    class="mb-2"
                >
                    <v-tooltip location="left">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props">mdi-cards</v-icon>
                        </template>
                        <span>Přidat do sbírky</span>
                    </v-tooltip>
                </v-btn>
                <v-btn
                    icon="mdi-format-list-bulleted"
                    color="primary"
                    size="x-small"
                    variant="text"
                    style="background-color: white;"
                >
                    <v-tooltip location="left">
                        <template v-slot:activator="{ props }">
                            <v-icon v-bind="props">mdi-format-list-bulleted</v-icon>
                        </template>
                        <span>Přidat do seznamu</span>
                    </v-tooltip>
                </v-btn>
            </div>
            
            <div class="card-number">
                <span class="text-caption">
                    {{ formattedNumber }}/{{ formattedSetTotal }}
                </span>
            </div>
            <div class="set-symbol">
                <v-img
                    :src="set.symbol_url"
                    :alt="set.name"
                    width="20"
                    height="20"
                    contain
                />
            </div>
        </div>
        
        <div class="card-footer">
            <v-img
                v-if="card.rarity"
                :src="rarityIcon"
                :alt="card.rarity"
                max-width="13"
                height="13"
                contain
                class="cursor-pointer"
            >
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <div v-bind="props" style="width: 100%; height: 100%;"></div>
                    </template>
                    <span>{{ card.rarity }}</span>
                </v-tooltip>
            </v-img>
            <v-btn 
                icon="mdi-heart-outline"
                color="grey-darken-1"
                size="x-small"
                variant="text"
            >
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-icon v-bind="props">mdi-heart</v-icon>
                    </template>
                    <span>Oblíbené</span>
                </v-tooltip>
            </v-btn>
        </div>
    </v-card>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    card: {
        type: Object,
        required: true
    },
    setTotal: {
        type: Number,
        required: true
    },
    set: {
        type: Object,
        required: true
    }
})

const formattedNumber = computed(() => {
    return String(props.card.number).padStart(3, '0')
})

const formattedSetTotal = computed(() => {
    return String(props.setTotal).padStart(3, '0')
})

const rarityIcon = computed(() => {
    if (!props.card.rarity) return null
    
    // Nahrazení mezer pomlčkami a převod na malá písmena
    const iconName = props.card.rarity.toLowerCase().replace(/\s+/g, '-')
    return `/images/symbols/${iconName}.svg`
})

const showDetail = () => {
    router.visit(route('sets.cards.show', {
        set: props.card.set_id,
        card: props.card.id
    }))
}
</script>

<style scoped>
.card-number {
    position: absolute;
    bottom: 0px;
    left: 0;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 2px 6px;
    border-radius: 4px;
    color: rgba(0, 0, 0, 0.6);
}

.set-symbol {
    position: absolute;
    bottom: 0px;
    right: 0;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 4px;
    border-radius: 4px;
}

.card-actions {
    position: absolute;
    top: 50px;
    right: 8px;
    display: flex;
    flex-direction: column;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 4px 2px 11px;
    margin-top: -4px;
}
</style> 