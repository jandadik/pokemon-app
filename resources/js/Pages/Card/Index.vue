<template>
    <div>
        <h1>Cards</h1>
        <ul>
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