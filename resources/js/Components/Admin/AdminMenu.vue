<template>
    <v-list>
        <v-list-item
            prepend-icon="mdi-view-list"
            title="Číselníky"
            :to="route('register-categories.index')"
            :active="route().current('register-categories.*')"
        />
        <!-- Zde můžeme přidávat další položky menu -->
    </v-list>
</template> 