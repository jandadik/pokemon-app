<template>
    <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'bottom' : 'start'"
        temporary
    >
        <Link 
            v-if="auth.isLoggedIn" 
            href="/profile" 
            @click="closeDrawer"
        >
            <v-list-item
                prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                :title="auth.user.name"
                nav
            >
            </v-list-item>
        </Link>

        <v-divider />

        <v-list density="compact" nav>
            <Link href="/admin" @click="closeDrawer">
                <v-list-item
                    v-if="auth.isLoggedIn && auth.can('admin.access')"
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
        <v-list density="compact" nav v-if="!auth.isLoggedIn">
            <Link href="/user-account/create" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-account-plus" title="Registrovat" value="register" />
            </Link>
        </v-list>
        <v-list density="compact" nav v-if="!auth.isLoggedIn">
            <Link href="/login" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-login" title="Přihlásit" value="login" />
            </Link>
        </v-list>

        <v-list density="compact" nav v-if="auth.isLoggedIn">
            <Link href="/logout" method="delete" as="button" @click="closeDrawer">
                <v-list-item prepend-icon="mdi-logout" title="Odhlásit" value="logout" />
            </Link>
        </v-list>
        
        <v-divider />
        
        <!-- Sekce Nastavení -->
        <v-list-subheader>Nastavení</v-list-subheader>
        <v-list density="compact" nav>
            <v-list-item prepend-icon="mdi-theme-light-dark" title="Téma aplikace">
                <template v-slot:append>
                    <ThemeSwitcher />
                </template>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import { Link } from '@inertiajs/vue3'
    import { useAuthStore } from '@/stores/authStore'
    import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'

    const auth = useAuthStore()
    const props = defineProps({
        modelValue: Boolean,
    })

    const emit = defineEmits(['update:modelValue'])

    const drawer = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    // const closeDrawer = () => {
    //     emit('update:modelValue', false)
    // }

    const closeDrawer = (event) => {
        try {
            emit('update:modelValue', false)
        } catch (error) {
            console.error('Chyba při zavírání menu:', error)
        }
    }
</script>