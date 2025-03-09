<template>
  <v-card class="mb-4">
    <v-card-title>{{ $t('account.email.title') }}</v-card-title>
    <v-card-text>
      <v-alert
        v-if="showEmailVerificationAlert"
        type="info"
        :title="$t('account.email.changed_title')"
        :text="$t('account.email.verification_sent')"
        class="mb-4"
        closable
        @click:close="showEmailVerificationAlert = false"
      ></v-alert>

      <v-alert
        v-if="!user.email_verified_at"
        type="warning"
        :title="$t('account.email.not_verified_title')"
        :text="$t('account.email.verification_needed')"
        class="mb-4"
      >
        <template v-slot:append>
          <v-btn
            color="warning"
            @click="resendVerification"
            :loading="verificationForm.processing"
          >
            {{ $t('account.email.resend_verification') }}
          </v-btn>
        </template>
      </v-alert>

      <v-form @submit.prevent="updateEmail" ref="emailFormRef" v-model="isEmailFormValid">
        <v-text-field
          v-model="emailForm.email"
          :label="$t('account.email.email')"
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
          {{ $t('account.email.change') }}
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

  emailForm.put(route('user.email.update'), {
    onSuccess: () => {
      isEmailFormValid.value = true
      showEmailVerificationAlert.value = true
      emit('success', $t('account.email.success_message'))
    },
    onError: () => {
      isEmailFormValid.value = false
    }
  })
}

const resendVerification = () => {
  verificationForm.post(route('auth.verification.send'), {
    onSuccess: () => {
      emit('success', $t('account.email.verification_resent'))
    }
  })
}
</script> 