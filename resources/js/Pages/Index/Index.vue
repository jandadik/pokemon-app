<template>
    <v-card v-if="authStore.user" class="mt-4">
        <v-card-title class="text-h5">
            Nastavení vašeho účtu
            <Link
                :href="route('user.profile')"
                class="float-right"
            >
                <v-btn
                    color="primary"
                    variant="text"
                >
                    Upravit nastavení
                </v-btn>
            </Link>
        </v-card-title>
        
        <v-card-text>
            <v-table>
                <thead>
                    <tr>
                        <th>Kategorie</th>
                        <th>Nastavení</th>
                        <th>Hodnota</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Notifikace -->
                    <tr>
                        <td rowspan="3">Notifikace</td>
                        <td>Emailové notifikace</td>
                        <td>
                            <v-icon :color="userStore.getEmailNotifications ? 'success' : 'error'">
                                {{ userStore.getEmailNotifications ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                        <td rowspan="3">
                            <Link :href="route('user.profile', { tab: 'notifications' })">
                                <v-btn
                                    variant="text"
                                    color="primary"
                                >
                                    Upravit
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>Push notifikace</td>
                        <td>
                            <v-icon :color="userStore.getPushNotifications ? 'success' : 'error'">
                                {{ userStore.getPushNotifications ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                    </tr>
                    <tr>
                        <td>Newsletter</td>
                        <td>
                            <v-icon :color="userStore.getNewsletter ? 'success' : 'error'">
                                {{ userStore.getNewsletter ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                    </tr>

                    <!-- Nastavení -->
                    <tr>
                        <td rowspan="2">Nastavení</td>
                        <td>Jazyk</td>
                        <td>{{ userStore.getLanguage === 'cs' ? 'Čeština' : 'English' }}</td>
                        <td rowspan="2">
                            <Link :href="route('user.profile', { tab: 'settings' })">
                                <v-btn
                                    variant="text"
                                    color="primary"
                                >
                                    Upravit
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>Téma</td>
                        <td>
                            {{ 
                                userStore.getTheme === 'light' ? 'Světlé' : 
                                userStore.getTheme === 'dark' ? 'Tmavé' : 
                                'Podle systému'
                            }}
                        </td>
                    </tr>

                    <!-- Zabezpečení -->
                    <tr>
                        <td rowspan="2">Zabezpečení</td>
                        <td>Upozornění na přihlášení</td>
                        <td>
                            <v-icon :color="userStore.getLoginNotifications ? 'success' : 'error'">
                                {{ userStore.getLoginNotifications ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                        <td rowspan="2">
                            <Link :href="route('user.profile', { tab: 'security' })">
                                <v-btn
                                    variant="text"
                                    color="primary"
                                >
                                    Upravit
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>Dvoufaktorové ověření</td>
                        <td>
                            <v-icon :color="authStore.user?.two_factor_enabled ? 'success' : 'error'">
                                {{ authStore.user?.two_factor_enabled ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </v-card-text>
    </v-card>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore'
import { useAuthStore } from '@/stores/authStore'
import { Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const userStore = useUserStore()
const authStore = useAuthStore()
const activeTab = ref('profile')

onMounted(() => {
    if (authStore.user) {
        console.log('Two Factor Auth Status:', {
            enabled: authStore.user.two_factor_enabled,
            user: authStore.user
        })
    }
})
</script>

<style scoped>
.v-table {
    background: transparent !important;
}
</style>