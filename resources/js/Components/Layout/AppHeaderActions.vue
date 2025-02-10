<template>
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-heart"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-heart</v-icon>
        <span v-else>Oblíbené</span>
    </v-btn>
    <v-btn 
        :icon="$vuetify.display.mobile"
        :variant="$vuetify.display.mobile ? 'plain' : 'text'"
        prepend-icon="mdi-magnify"
    >
        <v-icon v-if="$vuetify.display.mobile">mdi-magnify</v-icon>
        <span v-else>Hledat</span>
    </v-btn>
    <v-btn 
        icon="mdi-dots-vertical" 
        @click="$emit('toggle-user')"
    />
</template>

<script setup>
    import { useDisplay } from 'vuetify'
    
    defineEmits(['toggle-user']);
</script>