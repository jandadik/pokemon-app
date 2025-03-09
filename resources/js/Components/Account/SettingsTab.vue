<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.settings.title') }}</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="updateSettings" ref="settingsFormRef" v-model="isSettingsFormValid">
        <v-select
          v-model="settingsForm.language"
          :label="$t('account.settings.language')"
          :items="[
            { title: $t('account.settings.languages.cs'), value: 'cs' },
            { title: $t('account.settings.languages.en'), value: 'en' }
          ]"
          prepend-inner-icon="mdi-translate"
          class="mb-4"
        ></v-select>

        <v-select
          v-model="settingsForm.theme"
          :label="$t('account.settings.theme')"
          :items="[
            { title: $t('account.settings.themes.light'), value: 'light' },
            { title: $t('account.settings.themes.dark'), value: 'dark' },
            { title: $t('account.settings.themes.system'), value: 'system' }
          ]"
          prepend-inner-icon="mdi-theme-light-dark"
          class="mb-4"
        ></v-select>
        
        <!-- Ukázka vybraného tématu -->
        <v-card class="mt-4 mb-4 pa-4" :theme="previewTheme">
          <v-card-title class="text-center">{{ $t('account.settings.theme_preview') }}</v-card-title>
          <v-card-text>
            <div class="d-flex flex-column align-center">
              <v-icon size="48" class="mb-2">mdi-theme-light-dark</v-icon>
              <div class="text-center">
                {{ getThemeLabel(settingsForm.theme) }}
              </div>
              <v-chip color="primary" class="ma-2">{{ $t('account.settings.colors.primary') }}</v-chip>
              <v-chip color="secondary" class="ma-2">{{ $t('account.settings.colors.secondary') }}</v-chip>
              <v-chip color="accent" class="ma-2">{{ $t('account.settings.colors.accent') }}</v-chip>
              <v-chip color="error" class="ma-2">{{ $t('account.settings.colors.error') }}</v-chip>
            </div>
          </v-card-text>
        </v-card>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import { trans } from '@/i18n'

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['success', 'error'])

const userStore = useUserStore()
const settingsFormRef = ref(null)
const isSettingsFormValid = ref(true)

const settingsForm = useForm({
  language: userStore.getLanguage,
  theme: userStore.getTheme
})

// Detekce systémového nastavení
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)')
const isDarkMode = ref(systemPrefersDark.matches)

// Pro náhled témat
const previewTheme = computed(() => {
  if (settingsForm.theme === 'system') {
    return isDarkMode.value ? 'dark' : 'light'
  }
  return settingsForm.theme
})

// Vrátí popis tématu
function getThemeLabel(theme) {
  if (theme === 'light') return trans('account.settings.theme_light')
  if (theme === 'dark') return trans('account.settings.theme_dark')
  return trans('account.settings.theme_system', { 
    mode: isDarkMode.value ? trans('account.settings.theme_dark') : trans('account.settings.theme_light') 
  })
}

// Sledování změn v store a aktualizace formuláře
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
    if (newSettings) {
      settingsForm.language = newSettings.language
      settingsForm.theme = newSettings.theme
    }
  },
  { deep: true }
)

// Sledování změn ve formuláři 
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

const updateSettings = async () => {
  if (settingsFormRef.value) {
    const { valid } = await settingsFormRef.value.validate()
    if (!valid) return
  }

  settingsForm.put(route('user.settings.update'), {
    onSuccess: () => {
      isSettingsFormValid.value = true
      emit('success', trans('account.settings.success_message'))
    },
    onError: () => {
      isSettingsFormValid.value = false
      emit('error', trans('account.settings.error_message'))
    }
  })
}
</script> 