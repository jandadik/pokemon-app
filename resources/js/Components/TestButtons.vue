<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <h2>Test Vuetify Buttons</h2>
            </v-col>
        </v-row>
        
        <v-row class="my-2">
            <v-col cols="12" md="2">
                <v-btn
                    color="primary"
                    prepend-icon="mdi-check"
                >
                    Primary Button
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="error"
                    variant="outlined"
                    append-icon="mdi-close"
                >
                    Outlined Error
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="success"
                    variant="text"
                    size="large"
                >
                    Large Text
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="info"
                    variant="tonal"
                    rounded="pill"
                >
                    Rounded Tonal
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    icon="mdi-heart"
                    color="error"
                    size="x-large"
                    variant="flat"
                >
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="secondary"
                    variant="elevated"
                    stacked
                >
                    <v-icon>mdi-star</v-icon>
                    Stacked
                </v-btn>
            </v-col>
        </v-row>

        <v-row class="my-2">
            <v-col cols="12" md="2">
                <v-btn
                    color="warning"
                    loading
                >
                    Loading
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="purple"
                    variant="flat"
                    block
                >
                    Block Button
                </v-btn>
            </v-col>
            
            <v-col cols="12" md="2">
                <v-btn
                    color="grey-darken-3"
                    variant="outlined"
                    rounded="0"
                >
                    Square Button
                </v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
// Není potřeba žádná logika
</script> 