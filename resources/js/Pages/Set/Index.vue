<template>
    <v-container>
            <template v-for="(seriesSets, series) in sets" :key="series">
                <!-- Nadpis série -->
                <v-row>
                    <v-col cols="12">
                        <div class="text-h4 my-4 pb-2 border-b">{{ series }}</div>
                    </v-col>
                </v-row>

                <!-- Sety v sérii -->
                <v-row>
                    <v-col 
                        v-for="set in seriesSets" 
                        :key="set.id"
                        cols="12"
                        sm="6"
                        md="4"
                        lg="3"
                    >
                        <SetCard :set="set" />
                    </v-col>
                </v-row>

                <!-- Oddělovač mezi sériemi -->
                <v-divider class="my-4" />
            </template>
        </v-container>
</template>

<script setup>
import SetCard from '@/Components/Set/SetCard.vue'


const props = defineProps({
    sets: Object
})
</script>

<style lang="scss" scoped>

</style>