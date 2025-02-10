<template>
    <v-tabs
        :model-value="tabStore.activeTab"
        @update:model-value="tabStore.setActiveTab"
        align-tabs="title"
    >
        <v-tab
            v-for="item in tabStore.menuItems"
            :key="item.title"
            :text="item.title"
            :value="item.title"
        />
    </v-tabs>
</template>

<script setup>
    import { useTabStore } from '@/stores/tabStore'

    // defineProps({
    //     modelValue: [String, null]
    // })

    // defineEmits(['update:modelValue'])

    const tabStore = useTabStore()
</script>