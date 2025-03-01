<template>
  <v-container class="fill-height">
    <v-row justify="center">
      <v-col cols="12" md="6" lg="4">
        <v-card class="pa-4">
          <v-card-title class="text-center">Dvoufaktorové ověření</v-card-title>
          <v-card-subtitle class="text-center">Pro pokračování zadejte kód z vaší autentifikační aplikace</v-card-subtitle>
          
          <v-card-text>
            <v-form @submit.prevent="verify" ref="formRef" v-model="isFormValid">
              <v-text-field
                v-model="form.code"
                label="Ověřovací kód"
                type="number"
                required
                :error-messages="errors.code"
                autofocus
                class="mb-4"
              ></v-text-field>
              
              <v-btn 
                color="primary" 
                block 
                type="submit"
                :loading="form.processing"
                :disabled="!isFormValid || form.processing"
              >
                Ověřit
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const errors = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const formRef = ref(null)
const isFormValid = ref(true)

const form = useForm({
  code: ''
})

const verify = async () => {
  if (formRef.value) {
    const { valid } = await formRef.value.validate()
    if (!valid) return
  }

  form.post(route('two-factor.verify'), {
    onSuccess: () => {
      // Po úspěšném ověření budeme přesměrováni na původní stránku
    },
    onError: () => {
      isFormValid.value = false
    }
  })
}
</script> 