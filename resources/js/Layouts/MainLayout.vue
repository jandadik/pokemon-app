<template>
    <Link href="/">HomePage</Link>
    <Link href="/hello">ShopPage</Link>
    <div>
            <h1>hello</h1> {{counter}}
            <h2>hello double</h2> {{ store.doubleCount }}
            <v-btn @onclick="store.increment">klik</v-btn>
        </div>
    <slot></slot>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import {ref} from 'vue'
    import { useCounterStore } from '@/stores/counterStore'

    const store = useCounterStore()
    const counter = ref(0)
    setInterval(()=>counter.value++, 1000)

    setTimeout(() => {
        store.increment()
    }, 1000);
</script>
