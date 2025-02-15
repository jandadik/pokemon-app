<template>
    <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'bottom' : 'start'"
        temporary
    >
        <v-list-item
            v-if="user"
            prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
            :title="user.name"
            nav
        >
        </v-list-item>

        <v-divider />

        <v-list density="compact" nav>
            <Link href="/" @click="closeDrawer">
                <v-list-item
                    prepend-icon="mdi-cog"
                    title="Administrace"
                    value="admin"
                />
            </Link>

            <Link href="/" @click="closeDrawer">
                <v-list-item
                    prepend-icon="mdi-view-dashboard"
                    title="Dashboard"
                    value="dashboard"
                />
            </Link>

        </v-list>

        <v-divider />
        <v-list density="compact" nav v-if="!user">
            <Link href="/user-account/create" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-account-plus" title="Registrovat" value="register" />
            </Link>
        </v-list>
        <v-list density="compact" nav v-if="!user">
            <Link href="/login" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-login" title="Přihlásit" value="login" />
            </Link>
        </v-list>

        <v-list density="compact" nav v-if="user">
            <Link href="/logout" method="delete" as="button" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-logout" title="Odhlásit" value="logout" />
            </Link>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import { Link } from '@inertiajs/vue3'

    const props = defineProps({
        modelValue: Boolean,
        user: {
            type: Object,
            default: null
        }
    })

    const emit = defineEmits(['update:modelValue'])

    const drawer = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const closeDrawer = () => {
        emit('update:modelValue', false)
    }
</script>