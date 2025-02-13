<template>
    <AuthenticatedLayout>
        <v-container>
            <!-- Hlavička se jménem setu a návratem zpět -->
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center mb-4">
                        <v-btn
                            icon="mdi-arrow-left"
                            variant="text"
                            :href="route('sets.index')"
                            class="me-4"
                        />
                        <h1 class="text-h4">{{ set.name }}</h1>
                    </div>
                </v-col>
            </v-row>

            <!-- Seznam karet -->
            <v-row>
                <v-col 
                    v-for="card in cards" 
                    :key="card.id"
                    cols="6"          
                    sm="4"           
                    md="3"           
                    lg="2"           
                >
                    <CardItem 
                        :card="card"
                        :set-total="set.printed_total"
                        :set="set"
                    />
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CardItem from '@/Components/Cards/CardItem.vue'

defineProps({
    set: {
        type: Object,
        required: true
    },
    cards: {
        type: Array,
        required: true
    }
});
</script> 