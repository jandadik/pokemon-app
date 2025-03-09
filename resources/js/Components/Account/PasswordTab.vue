<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.password.title') }}</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="updatePassword" ref="passwordFormRef" v-model="isPasswordFormValid">
        <v-text-field
          v-model="passwordForm.current_password"
          :label="$t('account.password.current')"
          :type="showPassword ? 'text' : 'password'"
          required
          :error-messages="errors.current_password"
          prepend-inner-icon="mdi-lock"
          :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append-inner="showPassword = !showPassword"
        ></v-text-field>

        <v-text-field
          v-model="passwordForm.password"
          :label="$t('account.password.new')"
          :type="showPassword ? 'text' : 'password'"
          required
          :error-messages="errors.password"
          prepend-inner-icon="mdi-lock-plus"
        ></v-text-field>

        <v-text-field
          v-model="passwordForm.password_confirmation"
          :label="$t('account.password.confirm')"
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
          {{ $t('account.password.change') }}
        </v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['success'])

const passwordFormRef = ref(null)
const showPassword = ref(false)
const isPasswordFormValid = ref(true)

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updatePassword = async () => {
  if (passwordFormRef.value) {
    const { valid } = await passwordFormRef.value.validate()
    if (!valid) return
  }

  passwordForm.put(route('user.password.update'), {
    onSuccess: () => {
      passwordForm.reset()
      isPasswordFormValid.value = true
      emit('success', $t('account.password.success_message'))
    },
    onError: () => {
      isPasswordFormValid.value = false
    }
  })
}
</script> 