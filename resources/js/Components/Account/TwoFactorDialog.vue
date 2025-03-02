<template>
  <v-dialog v-model="dialogModel" max-width="500">
    <v-card>
      <v-card-title>Nastavení dvoufaktorového ověření</v-card-title>
      <v-card-text>
        <div v-if="!twoFactorEnabled">
          <p class="mb-4">Pro zvýšení bezpečnosti vašeho účtu doporučujeme aktivovat dvoufaktorové ověření.</p>
          
          <div class="d-flex justify-center mb-4">
            <v-progress-circular v-if="isLoading" indeterminate color="primary"></v-progress-circular>
            <v-img
              v-else
              :src="qrCode"
              max-width="200"
              contain
            ></v-img>
          </div>

          <p class="mb-4">Naskenujte QR kód pomocí autentifikační aplikace (např. Google Authenticator) a zadejte vygenerovaný kód níže.</p>

          <v-text-field
            v-model="form.code"
            label="Ověřovací kód"
            type="number"
            required
            :error-messages="errors.code"
          ></v-text-field>
        </div>
        <div v-else>
          <p class="mb-4">Opravdu chcete deaktivovat dvoufaktorové ověření? Tímto snížíte bezpečnost vašeho účtu.</p>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          variant="text"
          @click="dialogModel = false"
        >
          Zrušit
        </v-btn>
        <v-btn
          color="primary"
          @click="confirm"
          :loading="form.processing || isUpdating"
        >
          {{ twoFactorEnabled ? 'Deaktivovat' : 'Aktivovat' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useUserStore } from '@/stores/userStore'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue', 'success', 'error'])

const userStore = useUserStore()
const twoFactorEnabled = computed(() => {
  return userStore.parameters && 
         userStore.parameters.settings && 
         userStore.parameters.settings.two_factor_enabled
})

const qrCode = ref('')
const isLoading = ref(false)
const isUpdating = ref(false)

const form = useForm({
  code: ''
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Při otevření dialogu načteme QR kód, pokud je potřeba
watch(dialogModel, (newValue) => {
  if (newValue && !twoFactorEnabled.value) {
    generateQrCode()
  }
})

const generateQrCode = () => {
  qrCode.value = ''
  isLoading.value = true
  
  axios.get(route('two-factor.qr-code'))
    .then(response => {
      qrCode.value = response.data.svg
      isLoading.value = false
    })
    .catch(error => {
      isLoading.value = false
      emit('error', 'Nepodařilo se vygenerovat QR kód: ' + (error.response?.data?.message || error.message))
    })
}

const confirm = () => {
  if (!userStore.parameters || !userStore.parameters.settings) {
    emit('error', 'Nelze načíst nastavení uživatele')
    return
  }
  
  isUpdating.value = true
  
  if (!twoFactorEnabled.value) {
    // Aktivace 2FA
    if (form.code) {
      userStore.updateSecurity({
        two_factor_enabled: true
      }).then(() => {
        dialogModel.value = false
        emit('success', 'Dvoufaktorové ověření bylo úspěšně aktivováno')
        form.reset()
        isUpdating.value = false
      }).catch(error => {
        isUpdating.value = false
        emit('error', 'Nepodařilo se aktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message))
      })
    } else {
      isUpdating.value = false
      emit('error', 'Zadejte prosím ověřovací kód')
    }
  } else {
    // Deaktivace 2FA
    userStore.updateSecurity({
      two_factor_enabled: false
    }).then(() => {
      dialogModel.value = false
      emit('success', 'Dvoufaktorové ověření bylo úspěšně deaktivováno')
      isUpdating.value = false
    }).catch(error => {
      isUpdating.value = false
      emit('error', 'Nepodařilo se deaktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message))
    })
  }
}
</script> 