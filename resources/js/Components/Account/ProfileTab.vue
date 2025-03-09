<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.profile.title') }}</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="updateProfile" ref="profileFormRef" v-model="isProfileFormValid">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="profileForm.name"
              :label="$t('account.profile.name')"
              required
              :error-messages="errors.name"
              prepend-inner-icon="mdi-account"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6">
            <v-text-field
              v-model="profileForm.phone"
              :label="$t('account.profile.phone')"
              :error-messages="errors.phone"
              prepend-inner-icon="mdi-phone"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <v-textarea
              v-model="profileForm.bio"
              :label="$t('account.profile.bio')"
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
          {{ $t('account.profile.save') }}
        </v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

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

const emit = defineEmits(['success'])

const profileFormRef = ref(null)
const isProfileFormValid = ref(true)

// Inicializace formuláře s výchozími hodnotami
const profileForm = useForm({
  name: props.user.name || '',
  phone: props.user.phone || '',
  bio: props.user.bio || ''
})

const updateProfile = async () => {
  if (profileFormRef.value) {
    const { valid } = await profileFormRef.value.validate()
    if (!valid) return
  }

  profileForm.put(route('user.profile.update'), {
    onSuccess: () => {
      isProfileFormValid.value = true
      emit('success', $t('account.profile.success_message'))
    },
    onError: () => {
      isProfileFormValid.value = false
    },
    preserveScroll: true
  })
}
</script> 