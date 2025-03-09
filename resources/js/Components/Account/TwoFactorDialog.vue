<template>
  <v-dialog v-model="dialogModel" max-width="500px">
    <v-card>
      <v-card-title class="text-h5">
        {{ user.two_factor_enabled ? $t('account.security.two_factor_disable') : $t('account.security.two_factor_enable') }}
      </v-card-title>
      
      <v-card-text>
        <p v-if="user.two_factor_enabled">
          {{ $t('account.security.two_factor_disable_warning') }}
        </p>
        
        <div v-else>
          <p class="mb-4">{{ $t('account.security.two_factor_enable_info') }}</p>
          
          <div v-if="isLoading" class="text-center my-4">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
            <div class="mt-2">{{ $t('account.security.generating_qr') }}</div>
          </div>
          
          <div v-else-if="qrCode" class="text-center my-4">
            <!-- Zobrazení QR kódu jako SVG, pokud to je SVG formát -->
            <div v-if="qrCode.startsWith('<svg')" class="mb-4" v-html="qrCode"></div>
            
            <!-- Zobrazení QR kódu jako obrázku, pokud je to dataURL -->
            <div v-else-if="qrCode.startsWith('data:image')" class="mb-4">
              <img :src="qrCode" :alt="$t('account.security.qr_code_alt')" class="mx-auto" style="max-width: 100%;" />
            </div>
            
            <!-- Zobrazení OTP Auth URL jako text -->
            <v-alert v-else color="info" class="text-center mb-4" variant="tonal">
              <p class="mb-2"><strong>{{ $t('account.security.scan_qr_or_url') }}</strong></p>
              
              <p class="font-weight-bold text-break mb-2 user-select-all" style="word-break: break-all;">
                {{ qrCode }}
              </p>
              
              <v-btn
                color="primary"
                size="small"
                density="comfortable"
                class="mt-2 mb-2"
                @click="copyToClipboard(qrCode)"
              >
                {{ $t('account.security.copy_url') }}
              </v-btn>
            </v-alert>
            
            <!-- Zobrazení tajného klíče -->
            <v-alert v-if="otpSecret" color="info" class="mt-4" variant="tonal" density="compact">
              <p class="mb-1"><strong>{{ $t('account.security.secret_key') }}</strong></p>
              <p class="font-monospace mb-0 user-select-all">{{ otpSecret }}</p>
              <v-btn
                color="primary"
                size="x-small"
                density="comfortable"
                class="mt-2"
                @click="copyToClipboard(otpSecret)"
              >
                {{ $t('account.security.copy_key') }}
              </v-btn>
            </v-alert>
          </div>
          
          <div v-if="qrCode && !isCodeVerified">
            <v-form @submit.prevent="verifyCode" ref="codeFormRef" v-model="isCodeFormValid">
              <v-text-field
                v-model="codeForm.code"
                :label="$t('account.security.verification_code')"
                required
                :error-messages="errors.code"
                :rules="[v => !!v || $t('account.security.code_required'), v => /^\d{6}$/.test(v) || $t('account.security.code_format')]"
                prepend-inner-icon="mdi-key"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="mb-4"
              ></v-text-field>
              
              <v-checkbox 
                v-model="codeForm.remember" 
                :label="$t('account.security.remember_device')"
                color="primary"
                hide-details
                class="mb-4"
              ></v-checkbox>
              
              <v-btn 
                color="primary"
                type="submit"
                :loading="codeForm.processing"
                :disabled="!isCodeFormValid || codeForm.processing"
                class="mb-4"
              >
                {{ $t('account.security.verify_code') }}
              </v-btn>
            </v-form>
          </div>
        </div>
      </v-card-text>
      
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="error" variant="text" @click="close">
          {{ $t('account.general.cancel') }}
        </v-btn>
        <v-btn v-if="user.two_factor_enabled" color="primary" @click="disableTwoFactor" :loading="isProcessing">
          {{ $t('account.security.disable') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import QRCode from 'qrcode'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue', 'success', 'error'])

const qrCode = ref('')
const isLoading = ref(false)
const isUpdating = ref(false)
const otpSecret = ref('')

const form = useForm({
  code: '',
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Při otevření dialogu načteme QR kód, pokud je potřeba
watch(() => dialogModel.value, (newValue) => {
  if (newValue) {
    // Dialog se otevírá
    generateQrCode()
  } else {
    // Dialog se zavírá - resetujeme formulář
    form.reset()
    if (!props.user.two_factor_enabled) {
      qrCode.value = ''
      otpSecret.value = ''
    }
  }
})

const generateQrCode = () => {
  if (!props.user.two_factor_enabled) {
    isLoading.value = true
    otpSecret.value = ''
    
    axios.get(route('auth.two-factor.qr-code'))
      .then(response => {
        if (response.data && response.data.qr_code) {
          // Zpracování OTP Auth URL
          const otpAuthUrl = response.data.qr_code
          
          // Extrahování secret z URL (pro zobrazení uživateli)
          const secretMatch = otpAuthUrl.match(/secret=([A-Z0-9]+)/)
          if (secretMatch && secretMatch[1]) {
            otpSecret.value = secretMatch[1]
          }
          
          // Generování QR kódu pomocí knihovny
          QRCode.toDataURL(otpAuthUrl, { 
            errorCorrectionLevel: 'H',
            margin: 1,
            width: 250
          })
            .then(url => {
              qrCode.value = url
            })
            .catch(err => {
              // Použití otpAuthUrl jako záloha
              qrCode.value = otpAuthUrl
            })
          
        } else if (response.data && response.data.svg) {
          // Zpětná kompatibilita pro SVG formát
          qrCode.value = response.data.svg
        } else {
          emit('error', 'Nepodařilo se vygenerovat QR kód: Neplatný formát odpovědi')
        }
        
        isLoading.value = false
      })
      .catch(error => {
        emit('error', 'Nepodařilo se vygenerovat QR kód: ' + (error.response?.data?.message || error.message))
        isLoading.value = false
      })
  }
}

const confirm = () => {
  isUpdating.value = true
  
  if (!props.user.two_factor_enabled) {
    // Aktivace 2FA
    if (form.code) {
      // Ověříme kód pomocí správného endpointu z controlleru
      axios.post(route('auth.two-factor.enable'), { code: form.code })
        .then(() => {
          dialogModel.value = false
          emit('success', 'Dvoufaktorové ověření bylo úspěšně aktivováno')
          form.reset()
          // Přesměrujeme na aktuální stránku pro obnovení dat
          router.reload()
          isUpdating.value = false
        })
        .catch(error => {
          isUpdating.value = false
          emit('error', 'Neplatný ověřovací kód: ' + (error.response?.data?.message || error.message))
        })
    } else {
      isUpdating.value = false
      emit('error', 'Zadejte prosím ověřovací kód')
    }
  } else {
    // Deaktivace 2FA
    axios.post(route('auth.two-factor.disable'), {
      _method: 'DELETE'
    })
      .then(() => {
        dialogModel.value = false
        emit('success', 'Dvoufaktorové ověření bylo deaktivováno')
        // Přesměrujeme na aktuální stránku pro obnovení dat
        router.reload()
        isUpdating.value = false
      })
      .catch(error => {
        isUpdating.value = false
        emit('error', 'Nepodařilo se deaktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message))
      })
  }
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
    .then(() => {
      emit('success', 'Zkopírováno do schránky')
    })
    .catch(() => {
      emit('error', 'Nepodařilo se zkopírovat do schránky')
    })
}
</script> 