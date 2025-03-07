<template>
  <v-container class="fill-height">
    <v-row justify="center">
      <v-col cols="12" md="6" lg="4">
        <v-card class="pa-4">
          <v-card-title class="text-center">Dvoufaktorové ověření</v-card-title>
          <v-card-subtitle class="text-center">Pro pokračování zadejte kód z vaší autentifikační aplikace</v-card-subtitle>
          
          <v-card-text>
            <v-alert
              v-if="showError"
              type="error"
              text="Nesprávný ověřovací kód. Zkuste to prosím znovu."
              variant="tonal"
              class="mb-4"
              closable
              @click:close="showError = false"
            ></v-alert>
            
            <v-form @submit.prevent="verify" ref="formRef" v-model="isFormValid">
              <v-text-field
                v-model="form.code"
                label="Ověřovací kód"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="Zadejte 6místný kód"
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
                Ověřit
              </v-btn>
              
              <div class="text-center">
                <v-btn
                  variant="text"
                  size="small"
                  color="secondary"
                  :href="route('auth.logout')"
                  method="post"
                  as="button"
                  :disabled="form.processing"
                >
                  Odhlásit se
                </v-btn>
              </div>
            </v-form>
            
            <v-alert
              type="info"
              variant="tonal"
              class="mt-4 text-caption"
              density="compact"
            >
              <p class="mb-1">Nemáte přístup k vaší autentifikační aplikaci?</p>
              <p class="mb-0">Kontaktujte administrátora pro obnovení přístupu k vašemu účtu.</p>
            </v-alert>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
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

const formRef = ref(null)
const isFormValid = ref(true)
const showError = ref(false)

const form = useForm({
  code: ''
})

// Omezení vstupu pouze na čísla
const onlyNumbers = (event) => {
  const charCode = event.which ? event.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    event.preventDefault()
  }
}

const verify = async () => {
  if (form.code.length !== 6) {
    showError.value = true
    return
  }

  if (formRef.value) {
    const { valid } = await formRef.value.validate()
    if (!valid) return
  }

  form.post(route('auth.two-factor.verify'), {
    onSuccess: () => {
      // Po úspěšném ověření budeme přesměrováni na původní stránku
    },
    onError: () => {
      isFormValid.value = false
      showError.value = true
      form.reset()
    }
  })
}
</script> 