<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-center">{{ $t('auth.verify_email.title') }}</v-card-title>
          
          <v-card-text>
            <v-alert
              v-if="status === 'verification-link-sent'"
              type="success"
              class="mb-4"
            >
              {{ $t('auth.verify_email.success') }}
            </v-alert>

            <p class="text-body-1 mb-4">
              {{ $t('auth.verify_email.description') }}
            </p>

            <div class="d-flex gap-4">
              <v-form @submit.prevent="submit" class="flex-grow-1">
                <v-btn
                  color="primary"
                  type="submit"
                  block
                  :loading="form.processing"
                >
                  {{ $t('auth.verify_email.submit') }}
                </v-btn>
              </v-form>

              <v-form @submit.prevent="logout">
                <v-btn
                  color="error"
                  variant="text"
                  type="submit"
                  :loading="logoutForm.processing"
                >
                  {{ $t('auth.logout') }}
                </v-btn>
              </v-form>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
    default: null
  }
})

const form = useForm({})
const logoutForm = useForm({})

const submit = () => {
  form.post(route('auth.verification.send'))
}

const logout = () => {
  logoutForm.post(route('auth.logout'))
}
</script> 