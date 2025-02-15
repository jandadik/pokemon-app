<template>
    <v-tabs
        v-model="tabStore.activeTab"
        @update:model-value="handleTabChange"
        align-tabs="center"
    >
        <v-tab
            v-for="tab in tabStore.tabs"
            :key="tab.value"
            :value="tab.value"
        >
            {{ tab.title }}
        </v-tab>
    </v-tabs>
</template>

<script setup>
    import { useTabStore } from '@/stores/tabStore'
    import { router } from '@inertiajs/vue3'

    const tabStore = useTabStore()

    const handleTabChange = (value) => {
    
}
</script>