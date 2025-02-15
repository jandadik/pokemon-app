<template>
    <v-navigation-drawer
        v-model="drawer"
        :rail="rail"
        permanent
        @click="rail = false"
    >
        <v-list-item
            prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
            :title="name"
            nav
        >
            <template v-slot:append>
                <v-btn
                    variant="text"
                    icon="mdi-chevron-left"
                    @click.stop="rail = !rail"
                />
            </template>
        </v-list-item>

        <v-divider />

        <v-list density="compact" nav>
            <Link href="/">
                <v-list-item
                    prepend-icon="mdi-cog"
                    title="Administrace"
                    value="admin"
                />
            </Link>

            <Link href="/">
                <v-list-item
                    prepend-icon="mdi-view-dashboard"
                    title="Dashboard"
                    value="dashboard"
                />
            </Link>

            <v-list-item
                v-for="item in navigationItems"
                :key="item.value"
                :title="item.title"
                :value="item.value"
            />
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import { Link } from '@inertiajs/vue3'

    const props = defineProps({
        modelValue: Boolean
    })

    const emit = defineEmits(['update:modelValue'])

    const rail = ref(true)

    const drawer = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    // Přesunuto z AuthenticatedLayout.vue
    const navigationItems = computed(() => [
        {
            title: 'Foo',
            value: 'foo',
        },
        {
            title: 'Bar',
            value: 'bar',
        },
        {
            title: 'Fizz',
            value: 'fizz',
        },
        {
            title: 'Buzz',
            value: 'buzz',
        },
    ]);

    const name = "Jan Novák"
    const email = "jan.novak@example.com"
</script>