<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-center">{{ $t('auth.password_reset.title') }}</v-card-title>
          
          <v-card-text>
            <v-form 
              @submit.prevent="submit" 
              ref="formRef" 
              v-model="isFormValid"
              validate-on="submit"
            >
              <v-text-field
                v-model="form.email"
                :label="$t('auth.password_reset.email')"
                type="email"
                required
                :rules="emailRules"
                :error-messages="errors.email"
                @update:model-value="() => errors.email = ''"
                prepend-inner-icon="mdi-email"
                readonly
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                :label="$t('auth.password_reset.password')"
                :type="showPassword ? 'text' : 'password'"
                required
                :rules="passwordRules"
                :error-messages="errors.password"
                @update:model-value="() => errors.password = ''"
                prepend-inner-icon="mdi-lock"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"
                :append-icon-tabindex="-1"
              ></v-text-field>

              <v-text-field
                v-model="form.password_confirmation"
                :label="$t('auth.password_reset.password_confirm')"
                :type="showPassword ? 'text' : 'password'"
                required
                :rules="passwordConfirmationRules"
                :error-messages="errors.password_confirmation"
                @update:model-value="() => errors.password_confirmation = ''"
                prepend-inner-icon="mdi-lock-check"
              ></v-text-field>

              <v-btn
                color="primary"
                type="submit"
                block
                :loading="form.processing"
                :disabled="form.processing"
              >
                {{ $t('auth.password_reset.submit') }}
              </v-btn>

              <div class="text-center mt-4">
                <v-btn
                  variant="text"
                  color="primary"
                  @click="router.visit(route('auth.login'))"
                  class="text-none"
                >
                  {{ $t('auth.password_reset.back_to_login') }}
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
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  email: {
    type: String,
    required: true
  },
  token: {
    type: String,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  email: props.email,
  token: props.token,
  password: '',
  password_confirmation: ''
})

const isFormValid = ref(true)
const formRef = ref(null)
const showPassword = ref(false)

// Funkce pro získání validačních textů
const validationText = {
  emailRequired: 'E-mail je povinný',
  emailValid: 'E-mail musí být platný',
  passwordRequired: 'Heslo je povinné',
  passwordMinLength: 'Heslo musí mít alespoň 8 znaků',
  passwordUppercase: 'Heslo musí obsahovat alespoň jedno velké písmeno',
  passwordLowercase: 'Heslo musí obsahovat alespoň jedno malé písmeno',
  passwordNumber: 'Heslo musí obsahovat alespoň jednu číslici',
  passwordConfirmRequired: 'Potvrzení hesla je povinné',
  passwordConfirmMatch: 'Hesla se neshodují'
}

const emailRules = [
  v => !!v || validationText.emailRequired,
  v => /.+@.+\..+/.test(v) || validationText.emailValid,
]

const passwordRules = [
  v => !!v || validationText.passwordRequired,
  v => v?.length >= 8 || validationText.passwordMinLength,
  v => /[A-Z]/.test(v) || validationText.passwordUppercase,
  v => /[a-z]/.test(v) || validationText.passwordLowercase,
  v => /[0-9]/.test(v) || validationText.passwordNumber,
]

const passwordConfirmationRules = [
  v => !!v || validationText.passwordConfirmRequired,
  v => v === form.password || validationText.passwordConfirmMatch,
]

const submit = async () => {
  if (formRef.value) {
    const { valid } = await formRef.value.validate()
    if (!valid) {
      isFormValid.value = false
      return
    }
  }

  form.post(route('auth.password.update'), {
    onSuccess: () => {
      router.visit(route('auth.login'))
    },
    onError: () => {
      isFormValid.value = false
    }
  })
}
</script> 