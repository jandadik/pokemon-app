<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <v-tabs-window :model-value="tabStore.activeTab">
            <v-tabs-window-item
                v-for="item in tabStore.menuItems"
                :key="item.title"
                :value="item.title"
                >
                <v-container fluid>
                    <v-row>
                        <v-col
                        v-for="i in 198"
                        :key="i"
                        cols="6"
                        sm="4"
                        md="3"
                        lg="2"
                        xl="2"
                        >
                        <v-img
                            :lazy-src="`https://images.pokemontcg.io/sv1/${i}.png`"
                            :src="`https://images.pokemontcg.io/sv1/${i}.png`"
                            width="100%"
                            cover
                        ></v-img>
                        </v-col>
                    </v-row>
                </v-container>
            </v-tabs-window-item>
        </v-tabs-window>
        
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { useTabStore } from '@/stores/tabStore';

    const tabStore = useTabStore()
    
</script>