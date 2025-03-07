<template>
  <v-card class="mb-4">
    <v-card-title class="bg-error text-white">Smazání účtu</v-card-title>
    <v-card-text>
      <v-alert
        type="warning"
        class="mb-4"
      >
        <template v-slot:title>
          Nevratná akce
        </template>
        <p>Smazání účtu je nevratná akce. Všechna vaše data budou trvale odstraněna.</p>
      </v-alert>

      <v-form @submit.prevent="deleteAccount" ref="deleteFormRef" v-model="isDeleteFormValid">
        <v-text-field
          v-model="deleteForm.password"
          label="Pro potvrzení zadejte své heslo"
          type="password"
          required
          :error-messages="errors.password"
          prepend-inner-icon="mdi-lock"
        ></v-text-field>

        <v-checkbox
          v-model="deleteForm.confirm"
          label="Rozumím, že tato akce je nevratná"
          required
          :error-messages="errors.confirm"
          class="mb-4"
        ></v-checkbox>

        <v-btn 
          color="error" 
          type="submit"
          :loading="deleteForm.processing"
          :disabled="!isDeleteFormValid || deleteForm.processing || !deleteForm.confirm"
        >
          Smazat účet
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

const deleteFormRef = ref(null)
const isDeleteFormValid = ref(true)

const deleteForm = useForm({
  password: '',
  confirm: false
})

const deleteAccount = async () => {
  if (deleteFormRef.value) {
    const { valid } = await deleteFormRef.value.validate()
    if (!valid) return
  }

  deleteForm.delete(route('user.profile.destroy'), {
    onSuccess: () => {
      isDeleteFormValid.value = true
    },
    onError: () => {
      isDeleteFormValid.value = false
    }
  })
}
</script> 