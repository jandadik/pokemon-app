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
        <v-card v-if="activeTab === 'profile'" class="mb-4">
          <v-card-title>Osobní údaje</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateProfile" ref="profileFormRef" v-model="isProfileFormValid">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="profileForm.name"
                    label="Jméno"
                    required
                    :error-messages="errors.name"
                    prepend-inner-icon="mdi-account"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="profileForm.phone"
                    label="Telefon"
                    :error-messages="errors.phone"
                    prepend-inner-icon="mdi-phone"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    v-model="profileForm.bio"
                    label="O mně"
                    :error-messages="errors.bio"
                    prepend-inner-icon="mdi-text"
                    rows="3"
                  ></v-textarea>
                </v-col>
              </v-row>

              <v-btn 
                color="primary" 
                type="submit" 
                :loading="profileForm.processing"
                :disabled="!isProfileFormValid || profileForm.processing"
              >
                Uložit změny
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Heslo -->
        <v-card v-if="activeTab === 'password'" class="mb-4">
          <v-card-title>Změna hesla</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updatePassword" ref="passwordFormRef" v-model="isPasswordFormValid">
              <v-text-field
                v-model="passwordForm.current_password"
                label="Současné heslo"
                :type="showPassword ? 'text' : 'password'"
                required
                :error-messages="errors.current_password"
                prepend-inner-icon="mdi-lock"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"
              ></v-text-field>

              <v-text-field
                v-model="passwordForm.password"
                label="Nové heslo"
                :type="showPassword ? 'text' : 'password'"
                required
                :error-messages="errors.password"
                prepend-inner-icon="mdi-lock-plus"
              ></v-text-field>

              <v-text-field
                v-model="passwordForm.password_confirmation"
                label="Potvrzení nového hesla"
                :type="showPassword ? 'text' : 'password'"
                required
                prepend-inner-icon="mdi-lock-check"
              ></v-text-field>

              <v-btn 
                color="primary" 
                type="submit"
                :loading="passwordForm.processing"
                :disabled="!isPasswordFormValid || passwordForm.processing"
              >
                Změnit heslo
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Email -->
        <v-card v-if="activeTab === 'email'" class="mb-4">
          <v-card-title>Email</v-card-title>
          <v-card-text>
            <v-alert
              v-if="showEmailVerificationAlert"
              type="info"
              title="Email změněn"
              text="Na váš nový email byla odeslána zpráva s potvrzovacím odkazem. Pro dokončení změny emailu prosím klikněte na odkaz v této zprávě."
              class="mb-4"
              closable
              @click:close="showEmailVerificationAlert = false"
            ></v-alert>

            <v-alert
              v-if="!user.email_verified_at"
              type="warning"
              title="Email není ověřen"
              text="Pro plné využití účtu prosím ověřte svůj email."
              class="mb-4"
            >
              <template v-slot:append>
                <v-btn
                  color="warning"
                  @click="resendVerification"
                  :loading="verificationForm.processing"
                >
                  Znovu zaslat ověření
                </v-btn>
              </template>
            </v-alert>

            <v-form @submit.prevent="updateEmail" ref="emailFormRef" v-model="isEmailFormValid">
              <v-text-field
                v-model="emailForm.email"
                label="Email"
                type="email"
                required
                :error-messages="errors.email"
                prepend-inner-icon="mdi-email"
              ></v-text-field>

              <v-btn 
                color="primary" 
                type="submit"
                :loading="emailForm.processing"
                :disabled="!isEmailFormValid || emailForm.processing"
              >
                Změnit email
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Notifikace -->
        <v-card v-if="activeTab === 'notifications'" class="mb-4">
          <v-card-title>Nastavení notifikací</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateNotifications" ref="notificationsFormRef" v-model="isNotificationsFormValid">
              <v-switch
                v-model="notificationsForm.email_notifications"
                label="Emailové notifikace"
                color="primary"
                hide-details
                class="mb-4"
              ></v-switch>

              <v-switch
                v-model="notificationsForm.push_notifications"
                label="Push notifikace v prohlížeči"
                color="primary"
                hide-details
                class="mb-4"
              ></v-switch>

              <v-switch
                v-model="notificationsForm.newsletter"
                label="Odebírat newsletter"
                color="primary"
                hide-details
                class="mb-4"
              ></v-switch>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Zabezpečení -->
        <v-card v-if="activeTab === 'security'" class="mb-4">
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

        <!-- Nastavení -->
        <v-card v-if="activeTab === 'settings'" class="mb-4">
          <v-card-title>Nastavení účtu</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateSettings" ref="settingsFormRef" v-model="isSettingsFormValid">
              <v-select
                v-model="settingsForm.language"
                label="Jazyk rozhraní"
                :items="[
                  { title: 'Čeština', value: 'cs' },
                  { title: 'English', value: 'en' }
                ]"
                prepend-inner-icon="mdi-translate"
                class="mb-4"
              ></v-select>

              <v-select
                v-model="settingsForm.theme"
                label="Vzhled aplikace"
                :items="[
                  { title: 'Světlý', value: 'light' },
                  { title: 'Tmavý', value: 'dark' },
                  { title: 'Podle systému', value: 'system' }
                ]"
                prepend-inner-icon="mdi-theme-light-dark"
                class="mb-4"
              ></v-select>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Smazání účtu -->
        <v-card v-if="activeTab === 'delete'" class="mb-4">
          <v-card-title class="bg-error text-white">Smazání účtu</v-card-title>
          <v-card-text>
            <v-alert
              type="warning"
              class="mb-4"
            >
              <template v-slot:title>
                Nevratná akce
              </template>
              <p>Smazání účtu je nevratná akce. Všechna vaše data budou trvale odstraněna.</p>
            </v-alert>

            <v-form @submit.prevent="deleteAccount" ref="deleteFormRef" v-model="isDeleteFormValid">
              <v-text-field
                v-model="deleteForm.password"
                label="Pro potvrzení zadejte své heslo"
                type="password"
                required
                :error-messages="errors.password"
                prepend-inner-icon="mdi-lock"
              ></v-text-field>

              <v-checkbox
                v-model="deleteForm.confirm"
                label="Rozumím, že tato akce je nevratná"
                required
                :error-messages="errors.confirm"
                class="mb-4"
              ></v-checkbox>

              <v-btn 
                color="error" 
                type="submit"
                :loading="deleteForm.processing"
                :disabled="!isDeleteFormValid || deleteForm.processing || !deleteForm.confirm"
              >
                Smazat účet
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
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

const showPassword = ref(false)
const showTwoFactorDialog = ref(false)
const twoFactorQrCode = ref('')
const isLoadingQrCode = ref(false)

// Reference na formuláře
const profileFormRef = ref(null)
const passwordFormRef = ref(null)
const emailFormRef = ref(null)
const notificationsFormRef = ref(null)
const securityFormRef = ref(null)
const settingsFormRef = ref(null)
const deleteFormRef = ref(null)

// Validační stavy
const isProfileFormValid = ref(true)
const isPasswordFormValid = ref(true)
const isEmailFormValid = ref(true)
const isNotificationsFormValid = ref(true)
const isSecurityFormValid = ref(true)
const isSettingsFormValid = ref(true)
const isDeleteFormValid = ref(true)
const showEmailVerificationAlert = ref(false)
const isLoadingLoginHistory = ref(false)

// Inicializace store při načtení komponenty
onMounted(async () => {
  if (!userStore.isInitialized) {
    await userStore.fetchParameters()
  }
})

// Formuláře
const profileForm = useForm({
  name: props.user.name,
  phone: props.user.phone || '',
  bio: props.user.bio || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const emailForm = useForm({
  email: props.user.email
})

const notificationsForm = useForm({
  email_notifications: userStore.getEmailNotifications,
  push_notifications: userStore.getPushNotifications,
  newsletter: userStore.getNewsletter
})

const securityForm = useForm({
  two_factor_enabled: userStore.parameters.settings.two_factor_enabled,
  login_notifications: userStore.parameters.settings.login_notifications,
  sessions: props.user.sessions || []
})

const settingsForm = useForm({
  language: userStore.getLanguage,
  theme: userStore.getTheme
})

const deleteForm = useForm({
  password: '',
  confirm: false
})

const twoFactorForm = useForm({
  code: ''
})

const verificationForm = useForm({})

// Sledování změn v store a aktualizace formulářů
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
    // Aktualizace formuláře notifikací
    notificationsForm.email_notifications = newSettings.email_notifications
    notificationsForm.push_notifications = newSettings.push_notifications
    notificationsForm.newsletter = newSettings.newsletter

    // Aktualizace formuláře nastavení
    settingsForm.language = newSettings.language
    settingsForm.theme = newSettings.theme
    
    // Přidáme aktualizaci login_notifications
    securityForm.login_notifications = newSettings.login_notifications
    securityForm.two_factor_enabled = newSettings.two_factor_enabled
  },
  { deep: true }
)

// Watch pro notifikace
watch(
  () => ({
    email_notifications: notificationsForm.email_notifications,
    push_notifications: notificationsForm.push_notifications,
    newsletter: notificationsForm.newsletter
  }),
  (newValues) => {
    if (notificationsForm.processing) return
    userStore.updateNotifications(newValues)
  },
  { deep: true }
)

// Watch pro nastavení
watch(
  () => ({
    language: settingsForm.language,
    theme: settingsForm.theme
  }),
  (newValues) => {
    if (settingsForm.processing) return
    userStore.updateParameters(newValues)
  },
  { deep: true }
)

// Watch pro security
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

// Metody pro zpracování formulářů
const updateProfile = async () => {
  if (profileFormRef.value) {
    const { valid } = await profileFormRef.value.validate()
    if (!valid) return
  }

  profileForm.put(route('profile.update'), {
    onSuccess: () => {
      isProfileFormValid.value = true
    },
    onError: (errors) => {
      isProfileFormValid.value = false
    },
    preserveScroll: true
  })
}

const updatePassword = async () => {
  if (passwordFormRef.value) {
    const { valid } = await passwordFormRef.value.validate()
    if (!valid) return
  }

  passwordForm.put(route('password.update'), {
    onSuccess: () => {
      passwordForm.reset()
      isPasswordFormValid.value = true
    },
    onError: () => {
      isPasswordFormValid.value = false
    }
  })
}

const updateEmail = async () => {
  if (emailFormRef.value) {
    const { valid } = await emailFormRef.value.validate()
    if (!valid) return
  }

  emailForm.put(route('email.update'), {
    onSuccess: () => {
      isEmailFormValid.value = true
      showEmailVerificationAlert.value = true
    },
    onError: () => {
      isEmailFormValid.value = false
    }
  })
}

const updateNotifications = async () => {
  if (notificationsFormRef.value) {
    const { valid } = await notificationsFormRef.value.validate()
    if (!valid) return
  }

  notificationsForm.put(route('notifications.update'), {
    onSuccess: () => {
      isNotificationsFormValid.value = true
    },
    onError: () => {
      isNotificationsFormValid.value = false
    }
  })
}

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

const updateSettings = async () => {
  if (settingsFormRef.value) {
    const { valid } = await settingsFormRef.value.validate()
    if (!valid) return
  }

  settingsForm.put(route('settings.update'), {
    onSuccess: () => {
      isSettingsFormValid.value = true
    },
    onError: () => {
      isSettingsFormValid.value = false
    }
  })
}

const deleteAccount = async () => {
  if (deleteFormRef.value) {
    const { valid } = await deleteFormRef.value.validate()
    if (!valid) return
  }

  deleteForm.delete(route('profile.destroy'), {
    onSuccess: () => {
      isDeleteFormValid.value = true
    },
    onError: () => {
      isDeleteFormValid.value = false
    }
  })
}

const resendVerification = () => {
  verificationForm.post(route('verification.send'))
}

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

// Snackbar
const snackbar = ref({
  show: false,
  text: '',
  color: 'success'
})

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

const logoutSession = (sessionId) => {
  axios.delete(route('sessions.destroy', sessionId)).then(() => {
    securityForm.sessions = securityForm.sessions.filter(session => session.id !== sessionId)
  })
}

// Načtení historie přihlášení při otevření panelu historie
const loadLoginHistory = async () => {
  isLoadingLoginHistory.value = true;
  try {
    await userStore.fetchLoginHistory();
  } catch (error) {
    console.error('Chyba při načítání historie:', error);
  } finally {
    isLoadingLoginHistory.value = false;
  }
};

// Načtení historie při otevření záložky Security
watch(activeTab, (newVal) => {
  if (newVal === 'security') {
    loadLoginHistory();
  }
});

// Přidáme také načtení historie při inicializaci komponenty, pokud jsme na záložce Security
onMounted(() => {
  if (activeTab.value === 'security') {
    loadLoginHistory();
  }
});

// Funkce pro určení ikony na základě user-agent
const deviceIcon = (userAgent) => {
  userAgent = userAgent.toLowerCase();
  if (userAgent.includes('mobile') || userAgent.includes('android') || userAgent.includes('iphone')) {
    return 'mdi-cellphone';
  } else if (userAgent.includes('tablet') || userAgent.includes('ipad')) {
    return 'mdi-tablet';
  } else {
    return 'mdi-desktop-classic';
  }
};

// Funkce pro formátování user-agent do čitelnější podoby
const formatUserAgent = (userAgent) => {
  // Zjednodušené formátování - v reálné implementaci by bylo lepší použít knihovnu
  if (userAgent.includes('Firefox')) {
    return 'Firefox';
  } else if (userAgent.includes('Chrome') && !userAgent.includes('Edg')) {
    return 'Chrome';
  } else if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) {
    return 'Safari';
  } else if (userAgent.includes('Edg')) {
    return 'Edge';
  } else {
    return 'Prohlížeč';
  }
  // V reálné implementaci bychom přidali také informaci o operačním systému
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('cs', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Funkce pro zobrazení notifikace
const showSnackbar = (text, color = 'info') => {
  snackbar.value = {
    show: true,
    text,
    color
  }
}
</script> 