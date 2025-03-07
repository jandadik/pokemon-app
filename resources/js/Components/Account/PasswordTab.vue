<template>
  <v-card class="mb-4">
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
      emit('success', 'Heslo bylo úspěšně změněno')
    },
    onError: () => {
      isPasswordFormValid.value = false
    }
  })
}
</script> 