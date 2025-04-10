<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-center">{{ $t('auth.forgot_password.title') }}</v-card-title>
          
          <v-card-text>
            <v-alert
              v-if="status"
              type="success"
              class="mb-4"
            >
              {{ status }}
            </v-alert>

            <p class="text-body-1 mb-4">
              {{ $t('auth.forgot_password.description') }}
            </p>

            <v-form 
              @submit.prevent="submit" 
              ref="formRef" 
              v-model="isFormValid"
              validate-on="submit"
            >
              <v-text-field
                v-model="form.email"
                :label="$t('auth.forgot_password.email')"
                type="email"
                required
                :rules="emailRules"
                :error-messages="errors.email"
                @update:model-value="() => errors.email = ''"
                prepend-inner-icon="mdi-email"
              ></v-text-field>

              <v-btn
                color="primary"
                type="submit"
                block
                :loading="form.processing"
                :disabled="form.processing"
              >
                {{ $t('auth.forgot_password.submit') }}
              </v-btn>

              <div class="text-center mt-4">
                <v-btn
                  variant="text"
                  color="primary"
                  @click="router.visit(route('auth.login'))"
                  class="text-none"
                >
                  {{ $t('auth.forgot_password.back_to_login') }}
                </v-btn>
              </div>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  email: ''
})

const isFormValid = ref(true)
const formRef = ref(null)

// Funkce pro získání validačních textů
const validationText = {
  emailRequired: 'E-mail je povinný',
  emailValid: 'E-mail musí být platný'
}

const emailRules = [
  v => !!v || validationText.emailRequired,
  v => /.+@.+\..+/.test(v) || validationText.emailValid,
]

const submit = async () => {
  if (formRef.value) {
    const { valid } = await formRef.value.validate()
    if (!valid) {
      isFormValid.value = false
      return
    }
  }

  form.post(route('auth.password.email'), {
    onSuccess: () => {
      form.reset()
      if (formRef.value) {
        formRef.value.resetValidation()
      }
      // Po úspěšném odeslání přesměrujeme na login po 3 sekundách
      setTimeout(() => {
        router.visit(route('auth.login'))
      }, 3000)
    },
    onError: () => {
      isFormValid.value = false
    }
  })
}
</script> 