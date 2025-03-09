<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.security.title') }}</v-card-title>
    <v-card-text>
      <v-form ref="securityFormRef" v-model="isSecurityFormValid">
        <v-switch
          v-model="securityForm.two_factor_enabled"
          :label="$t('account.security.two_factor')"
          color="primary"
          hide-details
          class="mb-4"
          @change="handleTwoFactorChange"
        ></v-switch>

        <v-switch
          v-model="securityForm.login_notifications"
          :label="$t('account.security.login_notifications')"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>

        <!-- Historie přihlášení -->
        <v-expansion-panels class="mb-4">
          <v-expansion-panel>
            <v-expansion-panel-title>
              {{ $t('account.security.login_history') }}
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-skeleton-loader
                v-if="isLoadingLoginHistory"
                type="list-item-three-line"
                :loading="isLoadingLoginHistory"
                class="mb-4"
              ></v-skeleton-loader>
              
              <v-alert
                v-if="!isLoadingLoginHistory && loginHistory.length === 0"
                type="info"
                :text="$t('account.security.no_login_records')"
                class="mb-4"
              ></v-alert>
              
              
              <v-list lines="two" v-else>
                <v-list-item
                  v-for="(login, index) in loginHistory"
                  :key="index"
                  :subtitle="login.ip_address + (login.location ? ' · ' + login.location : '')"
                  :class="{ 'bg-error-lighten-5': login.is_suspicious }"
                  class="mb-1 rounded"
                  density="comfortable"
                >
                  <template v-slot:prepend>
                    <v-icon :color="login.is_current ? 'success' : (login.is_suspicious ? 'error' : '')" size="small">
                      {{ login.is_current ? 'mdi-check-circle' : deviceIcon(login.user_agent) }}
                    </v-icon>
                  </template>
                  
                  <!-- Desktop zobrazení -->
                  <div class="d-none d-sm-flex flex-row align-center w-100">
                    <v-list-item-title class="text-body-2">
                      {{ formatUserAgent(login.user_agent) }}
                      <v-chip
                        v-if="login.is_suspicious"
                        color="error"
                        size="x-small"
                        class="ml-2"
                      >
                        {{ $t('account.security.suspicious') }}
                      </v-chip>
                    </v-list-item-title>
                    
                    <v-spacer></v-spacer>
                    
                    <v-chip size="small" color="secondary" class="text-caption ml-2">
                      {{ formatDate(login.created_at) }}
                    </v-chip>
                  </div>
                  
                  <!-- Mobilní zobrazení -->
                  <div class="d-flex d-sm-none flex-column w-100">
                    <div class="d-flex justify-space-between align-center">
                      <v-list-item-title class="text-body-2">
                        {{ formatUserAgent(login.user_agent) }}
                        <v-chip
                          v-if="login.is_suspicious"
                          color="error"
                          size="x-small"
                          class="ml-1"
                        >
                          !
                        </v-chip>
                      </v-list-item-title>
                      
                      <v-chip size="x-small" color="secondary" class="text-caption ml-1">
                        {{ formatMobileDate(login.created_at) }}
                      </v-chip>
                    </div>
                    
                    <v-list-item-subtitle class="text-caption">
                      <span class="text-medium-emphasis">{{ login.ip_address }}</span>
                      <span v-if="login.location" class="text-disabled"> · {{ login.location }}</span>
                      <v-chip
                        v-if="login.is_suspicious"
                        color="error"
                        size="x-small"
                        class="ml-1"
                      >
                        {{ $t('account.security.suspicious') }}
                      </v-chip>
                    </v-list-item-subtitle>
                  </div>
                </v-list-item>
              </v-list>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
        
        <!-- Sekce aktivních relací -->
        <v-expansion-panels class="mb-4">
          <v-expansion-panel>
            <v-expansion-panel-title>
              {{ $t('account.security.active_sessions') }}
            </v-expansion-panel-title>
            <v-expansion-panel-text>
              <!-- Obsah panelu pro aktivní relace -->
              <v-btn 
                color="error" 
                variant="outlined"
                class="mb-2"
                @click="logoutOtherDevices"
                :loading="isLoggingOutOtherDevices"
              >
                {{ $t('account.security.logout_other_devices') }}
              </v-btn>
              
              <!-- ... active sessions code ... -->
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-form>
    </v-card-text>
  </v-card>
  
  <!-- Dialog pro dvoufaktorové ověření -->
  <TwoFactorDialog
    v-model="showTwoFactorDialog"
    :errors="errors"
    :user="props.user"
    @success="handleSuccess"
    @error="handleError"
  />
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import { storeToRefs } from 'pinia'
import TwoFactorDialog from '@/Components/Account/TwoFactorDialog.vue'
import axios from 'axios'
import { trans } from '@/i18n'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['success', 'error'])

const userStore = useUserStore()
const securityFormRef = ref(null)
const isSecurityFormValid = ref(true)
const isLoadingLoginHistory = ref(false)
const showTwoFactorDialog = ref(false)
const isLoggingOutOtherDevices = ref(false)

const { parameters, isLoading, loginHistory } = storeToRefs(userStore)

// Tab: Security - Formulář pro nastavení zabezpečení
const securityForm = useForm({
  two_factor_enabled: props.user.two_factor_enabled,
  login_notifications: userStore.parameters.settings.login_notifications,
  sessions: props.user.sessions || [],
})

// Sledování změn v userStore parameters - pouze pro login_notifications
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
    if (newSettings) {
      securityForm.login_notifications = newSettings.login_notifications
    }
  },
  { deep: true }
)

// Watch pro login_notifications
watch(
  () => securityForm.login_notifications,
  (newValue, oldValue) => {
    if (securityForm.processing) return
    
    if (newValue !== oldValue) {
      userStore.updateSecurity({ login_notifications: newValue })
        .then(() => {
          emit('success', trans('account.security.login_notifications_saved'))
        })
        .catch(error => {
          emit('error', trans('account.security.save_error'))
        })
    }
  }
)

// Watch pro two_factor_enabled z props.user
watch(
  () => props.user.two_factor_enabled,
  (newValue) => {
    if (securityForm.two_factor_enabled !== newValue) {
      securityForm.two_factor_enabled = newValue
    }
  },
  { immediate: true } // Zajistí okamžitou synchronizaci při inicializaci
)

// Funkce pro zpracování změny 2FA přepínače
const handleTwoFactorChange = (newValue) => {
  // Pokud se změnilo two_factor_enabled, otevřeme dialog
  if (newValue !== props.user.two_factor_enabled) {
    // Vrátíme hodnotu zpět, protože ji budeme měnit přes dialog
    securityForm.two_factor_enabled = props.user.two_factor_enabled
    showTwoFactorDialog.value = true
  }
}

// Funkce pro zpracování úspěšných operací
const handleSuccess = (message) => {
  emit('success', message)
}

// Funkce pro zpracování chyb
const handleError = (message) => {
  emit('error', message)
}

// Načtení historie přihlášení
const loadLoginHistory = async () => {
  isLoadingLoginHistory.value = true
  try {
    await userStore.fetchLoginHistory()
  } catch (error) {
    emit('error', trans('account.security.history_load_error'))
  } finally {
    isLoadingLoginHistory.value = false
  }
}

// Načtení historie přihlášení při inicializaci komponenty
onMounted(() => {
  loadLoginHistory()
})

// *** TAB: SECURITY - POMOCNÉ FUNKCE PRO HISTORII PŘIHLÁŠENÍ ***

// Funkce pro určení ikony na základě user-agent
const deviceIcon = (userAgent) => {
  userAgent = userAgent.toLowerCase()
  if (userAgent.includes('mobile') || userAgent.includes('android') || userAgent.includes('iphone')) {
    return 'mdi-cellphone'
  } else if (userAgent.includes('tablet') || userAgent.includes('ipad')) {
    return 'mdi-tablet'
  } else {
    return 'mdi-desktop-classic'
  }
}

// Funkce pro formátování user-agent do čitelnější podoby
const formatUserAgent = (userAgent) => {
  // Zjednodušené formátování - v reálné implementaci by bylo lepší použít knihovnu
  if (userAgent.includes('Firefox')) {
    return 'Firefox'
  } else if (userAgent.includes('Chrome') && !userAgent.includes('Edg')) {
    return 'Chrome'
  } else if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) {
    return 'Safari'
  } else if (userAgent.includes('Edg')) {
    return 'Edge'
  } else {
    return 'Prohlížeč'
  }
}

// Funkce pro formátování data
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('cs', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// Funkce pro formátování data pro mobilní zařízení - kratší formát
const formatMobileDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('cs', {
    day: '2-digit',
    month: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// Funkce pro odhlášení všech ostatních relací
const logoutOtherDevices = async () => {
  if (isLoggingOutOtherDevices.value) return
  
  isLoggingOutOtherDevices.value = true
  
  try {
    await axios.post(route('user.sessions.logout-others'))
    emit('success', trans('account.security.logout_others_success'))
    // Obnovit stránku pro aktualizaci seznamu relací
    window.location.reload()
  } catch (error) {
    emit('error', trans('account.security.logout_others_error'))
  } finally {
    isLoggingOutOtherDevices.value = false
  }
}
</script> 