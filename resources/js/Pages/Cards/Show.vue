<template>
    <AuthenticatedLayout>
        <v-container>
            <!-- Hlavička s návratem zpět -->
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center mb-4">
                        <v-btn
                            icon="mdi-arrow-left"
                            variant="text"
                            :href="route('sets.cards', set.id)"
                            class="me-4"
                        />
                    </div>
                </v-col>
            </v-row>

            <!-- Detail karty -->
            <v-row>
                <v-col 
                    cols="12"
                    md="6"
                    class="d-flex justify-center"
                >
                    <v-img
                        :src="card.img_small"
                        :alt="card.name"
                        max-height="600"
                        contain
                    />
                </v-col>
                
                <v-col 
                    cols="12"
                    md="6"
                >
                    <!-- Zde můžeme později přidat další informace o kartě -->
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    set: {
        type: Object,
        required: true
    },
    card: {
        type: Object,
        required: true
    }
})
</script> 