<template>
  <v-container>
    <v-row>
      <!-- Mobilní zobrazení - dropdown menu -->
      <v-col cols="12" class="d-md-none">
        <v-select
          v-model="activeTab"
          :items="[
            { title: 'Osobní údaje', value: 'profile', icon: 'mdi-account' },
            { title: 'Změna hesla', value: 'password', icon: 'mdi-lock' },
            { title: 'Email', value: 'email', icon: 'mdi-email', badge: !user.email_verified_at },
            { title: 'Notifikace', value: 'notifications', icon: 'mdi-bell' },
            { title: 'Zabezpečení', value: 'security', icon: 'mdi-shield-account' },
            { title: 'Nastavení', value: 'settings', icon: 'mdi-cog' },
            { title: 'Smazat účet', value: 'delete', icon: 'mdi-delete' }
          ]"
          item-title="title"
          item-value="value"
          label="Vyberte sekci"
          variant="outlined"
          density="comfortable"
        >
          <template v-slot:selection="{ item }">
            <v-icon :icon="item.raw.icon" class="mr-2"></v-icon>
            {{ item.raw.title }}
            <v-chip v-if="item.raw.value === 'email' && !user.email_verified_at" color="warning" size="x-small" class="ml-2">
              Neověřeno
            </v-chip>
          </template>
          
          <template v-slot:item="{ item, props }">
            <v-list-item v-bind="props" :prepend-icon="item.raw.icon" :title="item.raw.title">
              <template v-slot:append v-if="item.raw.badge">
                <v-chip color="warning" size="x-small">Neověřeno</v-chip>
              </template>
            </v-list-item>
          </template>
        </v-select>
      </v-col>

      <!-- Desktop zobrazení - boční menu -->
      <v-col cols="3" class="d-none d-md-block">
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

      <!-- Obsah tabů - na desktop vpravo, na mobilu pod menu -->
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

// Upraveno pro vyřešení objektu
watch(activeTab, (newTab) => {
  // Aktualizace URL
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

// *** INICIALIZACE STORE ***
onMounted(async () => {
  if (!userStore.isInitialized) {
    await userStore.fetchParameters()
  }
})

// *** SPOLEČNÉ KOMPONENTY ***

// Snackbar pro zobrazení notifikací
const snackbar = ref({
  show: false,
  text: '',
  color: 'success'
})

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