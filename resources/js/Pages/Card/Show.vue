<template>
    <div>
        <h1>{{ card.id }}</h1>
        <p>{{ card.name }}</p>
        <img :src="card.img_small" alt="Card Image">
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    card: Object
});
</script>

<style lang="scss" scoped>

</style>