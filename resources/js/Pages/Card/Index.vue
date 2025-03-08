<template>
    <div>
        <h1>{{ $t('app.cards.title') }}</h1>
        <p v-if="cards.length === 0">{{ $t('app.cards.no_cards') }}</p>
        <ul v-else>
            <li v-for="card in cards" :key="card.id">
                <Link :href="`/card/${card.id}`">
                    {{ card.name }}
                </Link>
            </li>   
        </ul>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    cards: Array
});
</script>

<style lang="scss" scoped>

</style>