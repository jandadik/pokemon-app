<template>
  <v-card class="mb-4">
    <v-card-title>Zabezpečení účtu</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="updateSecurity" ref="securityFormRef" v-model="isSecurityFormValid">
        <v-switch
          v-model="securityForm.two_factor_enabled"
          label="Dvoufaktorové ověření"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>

        <v-switch
          v-model="securityForm.login_notifications"
          label="Upozornění na nové přihlášení"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>

        <v-expansion-panels class="mb-4">
          <v-expansion-panel>
            <v-expansion-panel-title>
              Historie přihlášení
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
                text="Zatím nejsou k dispozici žádné záznamy o přihlášení."
                class="mb-4"
              ></v-alert>
              
              <v-list lines="two" v-else>
                <v-list-item
                  v-for="(login, index) in loginHistory"
                  :key="index"
                  :subtitle="login.ip_address + (login.location ? ' · ' + login.location : '')"
                  :class="{ 'bg-error-lighten-5': login.is_suspicious }"
                >
                  <template v-slot:prepend>
                    <v-icon :color="login.is_current ? 'success' : (login.is_suspicious ? 'error' : '')">
                      {{ login.is_current ? 'mdi-check-circle' : deviceIcon(login.user_agent) }}
                    </v-icon>
                  </template>
                  
                  <v-list-item-title>
                    {{ formatUserAgent(login.user_agent) }}
                    <v-chip
                      v-if="login.is_suspicious"
                      color="error"
                      size="x-small"
                      class="ml-2"
                    >
                      Podezřelé
                    </v-chip>
                  </v-list-item-title>
                  
                  <template v-slot:append>
                    <v-chip size="small" color="secondary" class="text-caption">
                      {{ formatDate(login.created_at) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import { storeToRefs } from 'pinia'

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

const emit = defineEmits(['success', 'show-two-factor-dialog', 'error'])

const userStore = useUserStore()
const securityFormRef = ref(null)
const isSecurityFormValid = ref(true)
const isLoadingLoginHistory = ref(false)

const { parameters, isLoading, loginHistory } = storeToRefs(userStore)

// Tab: Security - Formulář pro nastavení zabezpečení
const securityForm = useForm({
  two_factor_enabled: userStore.parameters.settings.two_factor_enabled,
  login_notifications: userStore.parameters.settings.login_notifications,
  sessions: props.user.sessions || [],
})

// Aktualizace formuláře, když se změní data v userStore
// onMounted(() => {
//   updateFormFromUserStore()
// })

// Sledování změn v userStore parameters
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
    if (newSettings) {
      securityForm.two_factor_enabled = newSettings.two_factor_enabled
      securityForm.login_notifications = newSettings.login_notifications
    }
  },
  { deep: true }
)

// Sledování změn v userStore.user
// watch(() => userStore.user, (newUser) => {
//   if (newUser && newUser.sessions) {
//     securityForm.sessions = newUser.sessions
//   }
// }, { deep: true })

// Pomocná funkce pro aktualizaci formuláře z userStore
// function updateFormFromUserStore() {
//   if (userStore.parameters && userStore.parameters.settings) {
//     securityForm.two_factor_enabled = userStore.parameters.settings.two_factor_enabled
//     securityForm.login_notifications = userStore.parameters.settings.login_notifications
//   }
  
//   if (userStore.user && userStore.user.sessions) {
//     securityForm.sessions = userStore.user.sessions
//   }
// }

// Sledování změn ve formuláři two_factor_enabled
// watch(() => securityForm.two_factor_enabled, (newValue) => {
//   if (userStore.parameters && userStore.parameters.settings && 
//       newValue !== userStore.parameters.settings.two_factor_enabled) {
//     emit('show-two-factor-dialog', newValue)
//     // Vracíme hodnotu zpět, protože změna se provede až po potvrzení dialogu
//     securityForm.two_factor_enabled = userStore.parameters.settings.two_factor_enabled
//   }
// })

// Sledování změn ve formuláři login_notifications
// watch(() => securityForm.login_notifications, (newValue) => {
//   if (userStore.parameters && userStore.parameters.settings && 
//       newValue !== userStore.parameters.settings.login_notifications) {
//     // Nebudeme aktualizovat přímo přes updateParameters,
//     // ale aktualizace proběhne při odeslání formuláře
//     securityForm.login_notifications = newValue;
//   }
// })
watch(
  () => ({
    login_notifications: securityForm.login_notifications
  }),
  (newValues) => {
    if (securityForm.processing) return
    userStore.updateSecurity(newValues)
  },
  { deep: true }
)

// Načtení historie přihlášení
const loadLoginHistory = async () => {
  isLoadingLoginHistory.value = true
  try {
    await userStore.fetchLoginHistory()
  } catch (error) {
    emit('error', 'Nepodařilo se načíst historii přihlášení')
  } finally {
    isLoadingLoginHistory.value = false
  }
}

// Načtení historie přihlášení při inicializaci komponenty
onMounted(() => {
  loadLoginHistory()
})

const updateSecurity = async () => {
  // if (formRef.value) {
  //   const { valid } = await formRef.value.validate()
  //   if (!valid) return
  // }

  // // Nejprve aktualizujeme data v store
  // try {
  //   await userStore.updateSecurity({
  //     login_notifications: form.login_notifications
  //   })
    
  //   // Poté odešleme formulář, pro případné další aktualizace
  //   form.put(route('security.update'), {
  //     onSuccess: () => {
  //       isFormValid.value = true
  //       emit('success', 'Nastavení zabezpečení bylo uloženo')
  //     },
  //     onError: () => {
  //       isFormValid.value = false
  //     },
  //     preserveScroll: true
  //   })
  // } catch (error) {
  //   emit('error', 'Nepodařilo se uložit nastavení zabezpečení: ' + error.message)
  //   isFormValid.value = false
  // }
}

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
</script> 