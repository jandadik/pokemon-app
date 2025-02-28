<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-center">Reset hesla</v-card-title>
          
          <v-card-text>
            <v-form 
              @submit.prevent="submit" 
              ref="formRef" 
              v-model="isFormValid"
              validate-on="submit"
            >
              <v-text-field
                v-model="form.email"
                label="Email"
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
                label="Nové heslo"
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
                label="Potvrzení hesla"
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
                Resetovat heslo
              </v-btn>

              <div class="text-center mt-4">
                <v-btn
                  variant="text"
                  color="primary"
                  @click="router.visit(route('login'))"
                  class="text-none"
                >
                  Zpět na přihlášení
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
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

const isFormValid = ref(true)
const formRef = ref(null)
const showPassword = ref(false)

const emailRules = [
  v => !!v || 'E-mail je povinný',
  v => /.+@.+\..+/.test(v) || 'E-mail musí být platný',
]

const passwordRules = [
  v => !!v || 'Heslo je povinné',
  v => v?.length >= 8 || 'Heslo musí mít alespoň 8 znaků',
]

const passwordConfirmationRules = [
  v => !!v || 'Potvrzení hesla je povinné',
  v => v === form.password || 'Hesla se neshodují',
]

const submit = async () => {
  if (formRef.value) {
    const { valid } = await formRef.value.validate()
    if (!valid) {
      isFormValid.value = false
      return
    }
  }

  form.post(route('password.store'), {
    onSuccess: () => {
      form.reset()
      if (formRef.value) {
        formRef.value.resetValidation()
      }
      // Po úspěšném resetu hesla přesměrujeme na login po 3 sekundách
      setTimeout(() => {
        router.visit(route('login'))
      }, 3000)
    },
    onError: () => {
      isFormValid.value = false
    }
  })
}
</script> 