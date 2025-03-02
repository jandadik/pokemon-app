<template>
  <v-card class="mb-4">
    <v-card-title>Email</v-card-title>
    <v-card-text>
      <v-alert
        v-if="showEmailVerificationAlert"
        type="info"
        title="Email změněn"
        text="Na váš nový email byla odeslána zpráva s potvrzovacím odkazem. Pro dokončení změny emailu prosím klikněte na odkaz v této zprávě."
        class="mb-4"
        closable
        @click:close="showEmailVerificationAlert = false"
      ></v-alert>

      <v-alert
        v-if="!user.email_verified_at"
        type="warning"
        title="Email není ověřen"
        text="Pro plné využití účtu prosím ověřte svůj email."
        class="mb-4"
      >
        <template v-slot:append>
          <v-btn
            color="warning"
            @click="resendVerification"
            :loading="verificationForm.processing"
          >
            Znovu zaslat ověření
          </v-btn>
        </template>
      </v-alert>

      <v-form @submit.prevent="updateEmail" ref="emailFormRef" v-model="isEmailFormValid">
        <v-text-field
          v-model="emailForm.email"
          label="Email"
          type="email"
          required
          :error-messages="errors.email"
          prepend-inner-icon="mdi-email"
        ></v-text-field>

        <v-btn 
          color="primary" 
          type="submit"
          :loading="emailForm.processing"
          :disabled="!isEmailFormValid || emailForm.processing"
        >
          Změnit email
        </v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['success'])

const emailFormRef = ref(null)
const isEmailFormValid = ref(true)
const showEmailVerificationAlert = ref(false)

// Inicializace formuláře s výchozí hodnotou
const emailForm = useForm({
  email: props.user.email
})

const verificationForm = useForm({})

const updateEmail = async () => {
  if (emailFormRef.value) {
    const { valid } = await emailFormRef.value.validate()
    if (!valid) return
  }

  emailForm.put(route('email.update'), {
    onSuccess: () => {
      isEmailFormValid.value = true
      showEmailVerificationAlert.value = true
      emit('success', 'Email byl úspěšně změněn')
    },
    onError: () => {
      isEmailFormValid.value = false
    }
  })
}

const resendVerification = () => {
  verificationForm.post(route('verification.send'), {
    onSuccess: () => {
      emit('success', 'Ověřovací email byl znovu odeslán')
    }
  })
}
</script> 