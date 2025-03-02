<template>
  <v-card class="mb-4">
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
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'

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

  settingsForm.put(route('settings.update'), {
    onSuccess: () => {
      isSettingsFormValid.value = true
    },
    onError: () => {
      isSettingsFormValid.value = false
    }
  })
}
</script> 