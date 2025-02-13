<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
    
        <div>
        <!-- Přímé použití v template -->
        <span>parametr: {{ userParametersStore.homepage }}</span><br>
        <span>parametr2: {{ userParametersStore.getParameter('homepage', 'nedef') }}</span>
        </div>
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
      
        
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';


    import { useUserParametersStore } from '@/stores/userParametersStore';

    const userParametersStore = useUserParametersStore();



    
</script>