<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="3">
        <!-- Navigační menu -->
        <v-card>
          <v-list>
            <v-list-item
              prepend-icon="mdi-account"
              title="Osobní údaje"
              value="profile"
              @click="activeTab = 'profile'"
            ></v-list-item>
            
            <v-list-item
              prepend-icon="mdi-lock"
              title="Změna hesla"
              value="password"
              @click="activeTab = 'password'"
            ></v-list-item>
            
            <v-list-item
              prepend-icon="mdi-email"
              title="Email"
              value="email"
              @click="activeTab = 'email'"
              :subtitle="!user.email_verified_at ? 'Neověřeno' : ''"
              :badge="!user.email_verified_at"
              badge-color="warning"
            ></v-list-item>

            <v-list-item
              prepend-icon="mdi-bell"
              title="Notifikace"
              value="notifications"
              @click="activeTab = 'notifications'"
            ></v-list-item>

            <v-list-item
              prepend-icon="mdi-shield-account"
              title="Zabezpečení"
              value="security"
              @click="activeTab = 'security'"
            ></v-list-item>

            <v-list-item
              prepend-icon="mdi-cog"
              title="Nastavení"
              value="settings"
              @click="activeTab = 'settings'"
            ></v-list-item>

            <v-list-item
              prepend-icon="mdi-delete"
              title="Smazat účet"
              value="delete"
              @click="activeTab = 'delete'"
              color="error"
            ></v-list-item>
          </v-list>
        </v-card>
      </v-col>

      <v-col cols="12" md="9">
        <!-- Profil -->
        <ProfileTab 
          v-if="activeTab === 'profile'" 
          :user="user"
          :errors="errors"
          @success="showSnackbar"
        />

        <!-- Heslo -->
        <PasswordTab 
          v-if="activeTab === 'password'" 
          :errors="errors"
          @success="showSnackbar"
        />

        <!-- Email -->
        <EmailTab 
          v-if="activeTab === 'email'" 
          :user="user"
          :errors="errors"
          @success="showSnackbar"
        />

        <!-- Notifikace -->
        <NotificationsTab 
          v-if="activeTab === 'notifications'" 
          :errors="errors"
          @success="showSnackbar"
          @error="showSnackbar($event, 'error')"
        />

        <!-- Zabezpečení -->
        <SecurityTab 
          v-if="activeTab === 'security'" 
          :user="user"
          :errors="errors"
          @success="showSnackbar"
          @show-two-factor-dialog="showTwoFactorDialog = true"
          @error="showSnackbar($event, 'error')"
        />

        <!-- Nastavení -->
        <SettingsTab 
          v-if="activeTab === 'settings'" 
          :errors="errors"
          @success="showSnackbar"
          @error="showSnackbar($event, 'error')"
        />

        <!-- Smazání účtu -->
        <DeleteAccountTab 
          v-if="activeTab === 'delete'" 
          :errors="errors"
        />
      </v-col>
    </v-row>

    <!-- Dialog pro dvoufaktorové ověření -->
    <v-dialog v-model="showTwoFactorDialog" max-width="500">
      <v-card>
        <v-card-title>Nastavení dvoufaktorového ověření</v-card-title>
        <v-card-text>
          <div v-if="!userStore.parameters.settings.two_factor_enabled">
            <p class="mb-4">Pro zvýšení bezpečnosti vašeho účtu doporučujeme aktivovat dvoufaktorové ověření.</p>
            
            <div class="d-flex justify-center mb-4">
              <v-progress-circular v-if="isLoadingQrCode" indeterminate color="primary"></v-progress-circular>
              <v-img
                v-else
                :src="twoFactorQrCode"
                max-width="200"
                contain
              ></v-img>
            </div>

            <p class="mb-4">Naskenujte QR kód pomocí autentifikační aplikace (např. Google Authenticator) a zadejte vygenerovaný kód níže.</p>

            <v-text-field
              v-model="twoFactorForm.code"
              label="Ověřovací kód"
              type="number"
              required
              :error-messages="errors.code"
            ></v-text-field>
          </div>
          <div v-else>
            <p class="mb-4">Opravdu chcete deaktivovat dvoufaktorové ověření? Tímto snížíte bezpečnost vašeho účtu.</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            variant="text"
            @click="showTwoFactorDialog = false"
          >
            Zrušit
          </v-btn>
          <v-btn
            color="primary"
            @click="confirmTwoFactor"
            :loading="twoFactorForm.processing"
          >
            {{ securityForm.two_factor_enabled ? 'Deaktivovat' : 'Aktivovat' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar pro oznámení -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3000"
    >
      {{ snackbar.text }}
      <template v-slot:actions>
        <v-btn
          variant="text"
          @click="snackbar.show = false"
        >
          Zavřít
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import { storeToRefs } from 'pinia'
import axios from 'axios'

// Import komponent
import ProfileTab from '@/Components/Account/ProfileTab.vue'
import PasswordTab from '@/Components/Account/PasswordTab.vue'
import EmailTab from '@/Components/Account/EmailTab.vue'
import DeleteAccountTab from '@/Components/Account/DeleteAccountTab.vue'
import NotificationsTab from '@/Components/Account/NotificationsTab.vue'
import SettingsTab from '@/Components/Account/SettingsTab.vue'
import SecurityTab from '@/Components/Account/SecurityTab.vue'

// *** ZÁKLADNÍ PROPS A PROMĚNNÉ ***
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

const page = usePage()
const userStore = useUserStore()
const { parameters, isLoading, loginHistory } = storeToRefs(userStore)

// *** NAVIGACE MEZI TABY ***
// Inicializace activeTab z URL parametru nebo výchozí hodnoty
const activeTab = ref(page.props.tab || 'profile')

// Sledování změn v URL parametrech
watch(() => page.props.tab, (newTab) => {
  if (newTab && ['profile', 'password', 'email', 'notifications', 'security', 'settings', 'delete'].includes(newTab)) {
    activeTab.value = newTab
  }
})

// Aktualizace URL při změně tabu
watch(activeTab, (newTab) => {
  if (newTab !== page.props.tab) {
    window.history.replaceState(
      {},
      '',
      newTab === 'profile' 
        ? route('profile') 
        : route('profile', { tab: newTab })
    )
  }
})

// *** SPOLEČNÉ PROMĚNNÉ ***

const showTwoFactorDialog = ref(false)
const twoFactorQrCode = ref('')
const isLoadingQrCode = ref(false)

// *** REFERENCE NA FORMULÁŘE ***

const securityFormRef = ref(null)       // Tab: Security MOVED

// *** VALIDAČNÍ STAVY FORMULÁŘŮ ***

const isSecurityFormValid = ref(true)       // Tab: Security MOVED
const isLoadingLoginHistory = ref(false)    // Tab: Security MOVED

// *** INICIALIZACE STORE ***
onMounted(async () => {
  if (!userStore.isInitialized) {
    await userStore.fetchParameters()
  }
})

// *** DEFINICE FORMULÁŘŮ ***


// Tab: Security - Formulář pro nastavení zabezpečení MOVED
const securityForm = useForm({
  two_factor_enabled: userStore.parameters.settings.two_factor_enabled,
  login_notifications: userStore.parameters.settings.login_notifications,
  sessions: props.user.sessions || []
})

// Tab: Security - Formulář pro dvoufaktorové ověření
const twoFactorForm = useForm({
  code: ''
})



// *** SLEDOVÁNÍ ZMĚN V STORE A AKTUALIZACE FORMULÁŘŮ *** MOVED
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
   
    // Tab: Security - Aktualizace formuláře zabezpečení
    securityForm.login_notifications = newSettings.login_notifications
    securityForm.two_factor_enabled = newSettings.two_factor_enabled
  },
  { deep: true }
)

// *** TAB: SECURITY - WATCH PRO ZABEZPEČENÍ ***

// Watch pro two_factor_enabled
watch(() => securityForm.two_factor_enabled, (newValue) => {
  if (newValue !== userStore.parameters.settings.two_factor_enabled) {
    showTwoFactorDialog.value = true
    if (newValue) {
      generateTwoFactorQrCode()
    }
  }
})

// Watch pro login_notifications
watch(() => securityForm.login_notifications, (newValue) => {
  if (newValue !== userStore.parameters.settings.login_notifications) {
    userStore.updateParameters({
      settings: {
        ...userStore.parameters.settings,
        login_notifications: newValue
      }
    })
  }
})

// Watch pro změny v userStore
watch(() => userStore.parameters.settings, (newSettings) => {
  if (securityForm.two_factor_enabled !== newSettings.two_factor_enabled) {
    securityForm.two_factor_enabled = newSettings.two_factor_enabled
  }
  if (securityForm.login_notifications !== newSettings.login_notifications) {
    securityForm.login_notifications = newSettings.login_notifications
  }
}, { deep: true })

// *** METODY PRO ZPRACOVÁNÍ FORMULÁŘŮ ***



// *** TAB: SECURITY - AKTUALIZACE ZABEZPEČENÍ ***
const updateSecurity = async () => {
  if (securityFormRef.value) {
    const { valid } = await securityFormRef.value.validate()
    if (!valid) return
  }

  securityForm.put(route('security.update'), {
    onSuccess: () => {
      isSecurityFormValid.value = true
    },
    onError: (errors) => {
      isSecurityFormValid.value = false
    },
    preserveScroll: true
  })
}

// *** TAB: SECURITY - GENEROVÁNÍ QR KÓDU PRO 2FA ***
const generateTwoFactorQrCode = () => {
  twoFactorQrCode.value = ''
  isLoadingQrCode.value = true
  
  axios.get(route('two-factor.qr-code'))
    .then(response => {
      twoFactorQrCode.value = response.data.svg
      isLoadingQrCode.value = false
    })
    .catch(error => {
      isLoadingQrCode.value = false
      showSnackbar('Nepodařilo se vygenerovat QR kód: ' + (error.response?.data?.message || error.message), 'error')
    })
}

// *** SPOLEČNÉ KOMPONENTY ***

// Snackbar pro zobrazení notifikací
const snackbar = ref({
  show: false,
  text: '',
  color: 'success'
})

// *** TAB: SECURITY - POTVRZENÍ 2FA DIALOGU ***
const confirmTwoFactor = () => {
  if (!userStore.parameters.settings.two_factor_enabled) {
    // Aktivace 2FA
    if (twoFactorForm.code) {
      userStore.updateParameters({
        settings: {
          ...userStore.parameters.settings,
          two_factor_enabled: true
        }
      }).then(() => {
        showTwoFactorDialog.value = false
        showSnackbar('Dvoufaktorové ověření bylo úspěšně aktivováno', 'success')
      }).catch(error => {
        showSnackbar('Nepodařilo se aktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message), 'error')
      })
    } else {
      showSnackbar('Zadejte prosím ověřovací kód', 'error')
    }
  } else {
    // Deaktivace 2FA
    userStore.updateParameters({
      settings: {
        ...userStore.parameters.settings,
        two_factor_enabled: false
      }
    }).then(() => {
      showTwoFactorDialog.value = false
      showSnackbar('Dvoufaktorové ověření bylo úspěšně deaktivováno', 'success')
    }).catch(error => {
      showSnackbar('Nepodařilo se deaktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message), 'error')
    })
  }
}

// *** TAB: SECURITY - ODHLÁŠENÍ RELACE ***
const logoutSession = (sessionId) => {
  axios.delete(route('sessions.destroy', sessionId)).then(() => {
    securityForm.sessions = securityForm.sessions.filter(session => session.id !== sessionId)
  })
}

// *** SPOLEČNÉ FUNKCE ***

// Funkce pro zobrazení notifikace
const showSnackbar = (text, color = 'info') => {
  snackbar.value = {
    show: true,
    text,
    color
  }
}
</script> 