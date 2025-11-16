<template>
  <div v-if="authStore.user" class="mt-4">
    <div class="card">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-text-primary dark:text-text-primary-dark">
          {{ $t('app.account_settings') }}
        </h2>
        <Link :href="route('user.profile')">
          <button class="btn-text text-primary">
            {{ $trans('app.buttons.edit_settings') }}
          </button>
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-border dark:border-border-dark">
              <th class="text-left py-3 px-4 font-medium">{{ $t('app.category') }}</th>
              <th class="text-left py-3 px-4 font-medium">{{ $t('app.setting') }}</th>
              <th class="text-left py-3 px-4 font-medium">{{ $t('app.value') }}</th>
              <th class="text-left py-3 px-4 font-medium">{{ $t('app.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <!-- Notifications -->
            <tr class="border-b border-border dark:border-border-dark">
              <td rowspan="3" class="py-3 px-4 align-top">{{ $t('app.notifications') }}</td>
              <td class="py-3 px-4">{{ $t('app.email_notifications') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', userStore.getEmailNotifications ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
              <td rowspan="3" class="py-3 px-4 align-top">
                <Link :href="route('user.profile', { tab: 'notifications' })">
                  <button class="btn-text text-primary">
                    {{ $trans('app.buttons.edit') }}
                  </button>
                </Link>
              </td>
            </tr>
            <tr class="border-b border-border dark:border-border-dark">
              <td class="py-3 px-4">{{ $t('app.push_notifications') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', userStore.getPushNotifications ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
            </tr>
            <tr class="border-b border-border dark:border-border-dark">
              <td class="py-3 px-4">{{ $t('app.newsletter') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', userStore.getNewsletter ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
            </tr>

            <!-- Settings -->
            <tr class="border-b border-border dark:border-border-dark">
              <td rowspan="2" class="py-3 px-4 align-top">{{ $t('app.settings') }}</td>
              <td class="py-3 px-4">{{ $t('app.language') }}</td>
              <td class="py-3 px-4">{{ userStore.getLanguage === 'cs' ? 'Čeština' : 'English' }}</td>
              <td rowspan="2" class="py-3 px-4 align-top">
                <Link :href="route('user.profile', { tab: 'settings' })">
                  <button class="btn-text text-primary">
                    {{ $trans('app.buttons.edit') }}
                  </button>
                </Link>
              </td>
            </tr>
            <tr class="border-b border-border dark:border-border-dark">
              <td class="py-3 px-4">{{ $t('app.theme') }}</td>
              <td class="py-3 px-4">
                {{
                  userStore.getTheme === 'light' ? 'Světlé' :
                  userStore.getTheme === 'dark' ? 'Tmavé' :
                  'Podle systému'
                }}
              </td>
            </tr>

            <!-- Collections -->
            <tr class="border-b border-border dark:border-border-dark">
              <td class="py-3 px-4">{{ $t('app.collections') }}</td>
              <td class="py-3 px-4">{{ $t('account.settings.collections.auto_save_to_default') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', userStore.getAutoSaveToDefaultCollection ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
              <td class="py-3 px-4">
                <Link :href="route('user.profile', { tab: 'settings' })">
                  <button class="btn-text text-primary">
                    {{ $trans('app.buttons.edit') }}
                  </button>
                </Link>
              </td>
            </tr>

            <!-- Security -->
            <tr class="border-b border-border dark:border-border-dark">
              <td rowspan="2" class="py-3 px-4 align-top">{{ $t('app.security') }}</td>
              <td class="py-3 px-4">{{ $t('app.login_notifications') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', userStore.getLoginNotifications ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
              <td rowspan="2" class="py-3 px-4 align-top">
                <Link :href="route('user.profile', { tab: 'security' })">
                  <button class="btn-text text-primary">
                    {{ $trans('app.buttons.edit') }}
                  </button>
                </Link>
              </td>
            </tr>
            <tr>
              <td class="py-3 px-4">{{ $t('app.two_factor_authentication') }}</td>
              <td class="py-3 px-4">
                <span :class="['mdi text-lg', authStore.user?.two_factor_enabled ? 'mdi-check text-success' : 'mdi-close text-error']"></span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore'
import { useAuthStore } from '@/stores/authStore'
import { Link } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const userStore = useUserStore()
const authStore = useAuthStore()

onMounted(() => {
  if (authStore.user) {
    console.log('Two Factor Auth Status:', {
      enabled: authStore.user.two_factor_enabled,
      user: authStore.user
    })
  }
})
</script>
