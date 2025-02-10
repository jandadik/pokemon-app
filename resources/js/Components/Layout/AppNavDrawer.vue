<template>
    <v-navigation-drawer
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
        :location="$vuetify.display.mobile ? 'bottom' : undefined"
        temporary
    >
        <v-list>
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
    import { computed } from 'vue'
    import { useDisplay } from 'vuetify'

    defineProps({
        modelValue: Boolean
    })

    defineEmits(['update:modelValue'])

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
</script>