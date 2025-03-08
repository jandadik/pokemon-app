<template>
    <v-card v-if="authStore.user" class="mt-4">
        <v-card-title class="text-h5">
            {{ $t('app.account_settings') }}
            <Link
                :href="route('user.profile')"
                class="float-right"
            >
                <v-btn
                    color="primary"
                    variant="text"
                >
                    {{ $trans('app.buttons.edit_settings') }}
                </v-btn>
            </Link>
        </v-card-title>
        
        <v-card-text>
            <v-table>
                <thead>
                    <tr>
                        <th>{{ $t('app.category') }}</th>
                        <th>{{ $t('app.setting') }}</th>
                        <th>{{ $t('app.value') }}</th>
                        <th>{{ $t('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Notifikace -->
                    <tr>
                        <td rowspan="3">{{ $t('app.notifications') }}</td>
                        <td>{{ $t('app.email_notifications') }}</td>
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
                                    {{ $trans('app.buttons.edit') }}
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $t('app.push_notifications') }}</td>
                        <td>
                            <v-icon :color="userStore.getPushNotifications ? 'success' : 'error'">
                                {{ userStore.getPushNotifications ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $t('app.newsletter') }}</td>
                        <td>
                            <v-icon :color="userStore.getNewsletter ? 'success' : 'error'">
                                {{ userStore.getNewsletter ? 'mdi-check' : 'mdi-close' }}
                            </v-icon>
                        </td>
                    </tr>

                    <!-- Nastavení -->
                    <tr>
                        <td rowspan="2">{{ $t('app.settings') }}</td>
                        <td>{{ $t('app.language') }}</td>
                        <td>{{ userStore.getLanguage === 'cs' ? 'Čeština' : 'English' }}</td>
                        <td rowspan="2">
                            <Link :href="route('user.profile', { tab: 'settings' })">
                                <v-btn
                                    variant="text"
                                    color="primary"
                                >
                                    {{ $trans('app.buttons.edit') }}
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $t('app.theme') }}</td>
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
                        <td rowspan="2">{{ $t('app.security') }}</td>
                        <td>{{ $t('app.login_notifications') }}</td>
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
                                    {{ $trans('app.buttons.edit') }}
                                </v-btn>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $t('app.two_factor_authentication') }}</td>
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