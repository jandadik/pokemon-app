<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.notifications.title') }}</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="updateNotifications" ref="notificationsFormRef" v-model="isNotificationsFormValid">
        <v-switch
          v-model="notificationsForm.email_notifications"
          :label="$t('account.notifications.email')"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>

        <v-switch
          v-model="notificationsForm.push_notifications"
          :label="$t('account.notifications.push')"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>

        <v-switch
          v-model="notificationsForm.newsletter"
          :label="$t('account.notifications.newsletter')"
          color="primary"
          hide-details
          class="mb-4"
        ></v-switch>
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
const notificationsFormRef = ref(null)
const isNotificationsFormValid = ref(true)

const notificationsForm = useForm({
  email_notifications: userStore.parameters.settings.email_notifications,
  push_notifications: userStore.parameters.settings.push_notifications,
  newsletter: userStore.parameters.settings.newsletter
})

// Sledování změn v store a aktualizace formuláře
watch(
  () => userStore.parameters.settings,
  (newSettings) => {
    if (newSettings) {
      notificationsForm.email_notifications = newSettings.email_notifications
      notificationsForm.push_notifications = newSettings.push_notifications
      notificationsForm.newsletter = newSettings.newsletter
    }
  },
  { deep: true }
)

// Sledování změn ve formuláři
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

const updateNotifications = async () => {
  if (notificationsFormRef.value) {
    const { valid } = await notificationsFormRef.value.validate()
    if (!valid) return
  }

  notificationsForm.put(route('user.notifications.update'), {
    onSuccess: () => {
      isNotificationsFormValid.value = true
      emit('success', $t('account.notifications.success_message'))
    },
    onError: () => {
      isNotificationsFormValid.value = false
      emit('error', $t('account.notifications.error_message'))
    }
  })
}
</script> 