<template>
  <v-container class="fill-height">
    <v-row justify="center">
      <v-col cols="12" md="6" lg="4">
        <v-card class="pa-4">
          <v-card-title class="text-center">{{ $t('auth.two_factor.title') }}</v-card-title>
          <v-card-subtitle class="text-center">{{ $t('auth.two_factor.subtitle') }}</v-card-subtitle>
          
          <v-card-text>
            <v-alert
              v-if="showError"
              type="error"
              :text="$t('auth.two_factor.invalid_code')"
              variant="tonal"
              class="mb-4"
              closable
              @click:close="showError = false"
            ></v-alert>
            
            <v-form @submit.prevent="verify" ref="formRef" v-model="isFormValid">
              <v-text-field
                v-model="form.code"
                :label="$t('auth.two_factor.code')"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                :placeholder="$t('auth.two_factor.code_placeholder')"
                required
                :error-messages="errors.code"
                autofocus
                class="mb-4"
                variant="outlined"
                autocomplete="one-time-code"
                @keypress="onlyNumbers"
              ></v-text-field>
              
              <v-btn 
                color="primary" 
                block 
                type="submit"
                :loading="form.processing"
                :disabled="!isFormValid || form.processing || form.code.length !== 6"
                class="mb-4"
              >
                {{ $t('auth.two_factor.submit') }}
              </v-btn>
              
              <div class="text-center">
                <v-btn
                  variant="text"
                  color="primary"
                  @click="useRecoveryCode = !useRecoveryCode"
                  class="text-none mb-2"
                >
                  {{ $t('auth.two_factor.recovery') }}
                </v-btn>
              </div>
            </v-form>
            
            <v-alert
              type="info"
              variant="tonal"
              class="mt-4 text-caption"
              density="compact"
            >
              <p class="mb-1">{{ $t('auth.two_factor.no_access') }}</p>
              <p class="mb-0">{{ $t('auth.two_factor.contact_admin') }}</p>
            </v-alert>
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
  errors: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  code: ''
})

const isFormValid = ref(true)
const formRef = ref(null)
const showError = ref(false)
const useRecoveryCode = ref(false)

// Povolí pouze číselné vstupy pro pole kódu
const onlyNumbers = (e) => {
  const charCode = e.which ? e.which : e.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    e.preventDefault()
  }
}

const verify = () => {
  form.post(route('auth.two-factor.challenge'), {
    onSuccess: () => {
      form.reset()
      router.visit(route('index'))
    },
    onError: () => {
      showError.value = true
      form.reset()
      if (formRef.value) {
        formRef.value.$el.querySelector('input').focus()
      }
    }
  })
}
</script> 