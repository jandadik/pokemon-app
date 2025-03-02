<template>
  <v-dialog v-model="dialogModel" max-width="500px">
    <v-card>
      <v-card-title class="text-h5">
        {{ twoFactorEnabled ? 'Deaktivovat dvoufaktorové ověření' : 'Aktivovat dvoufaktorové ověření' }}
      </v-card-title>
      
      <v-card-text>
        <p v-if="twoFactorEnabled">
          Opravdu chcete deaktivovat dvoufaktorové ověření? Tím se sníží bezpečnost vašeho účtu.
        </p>
        
        <div v-else>
          <p class="mb-4">Pro aktivaci dvoufaktorového ověření naskenujte následující QR kód pomocí autentifikační aplikace (např. Google Authenticator, Authy nebo Microsoft Authenticator):</p>
          
          <div v-if="isLoading" class="text-center my-4">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
            <div class="mt-2">Generuji QR kód...</div>
          </div>
          
          <div v-else-if="qrCode" class="text-center my-4">
            <!-- Zobrazení QR kódu jako SVG, pokud to je SVG formát -->
            <div v-if="qrCode.startsWith('<svg')" class="mb-4" v-html="qrCode"></div>
            
            <!-- Zobrazení QR kódu jako obrázku, pokud je to dataURL -->
            <div v-else-if="qrCode.startsWith('data:image')" class="mb-4">
              <img :src="qrCode" alt="QR kód pro dvoufaktorové ověření" class="mx-auto" style="max-width: 100%;" />
            </div>
            
            <!-- Zobrazení OTP Auth URL jako text -->
            <v-alert v-else color="info" class="text-center mb-4" variant="tonal">
              <p class="mb-2"><strong>Naskenujte QR kód nebo zadejte URL do vaší autentifikační aplikace:</strong></p>
              
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
                Zkopírovat URL
              </v-btn>
            </v-alert>
            
            <!-- Zobrazení tajného klíče -->
            <v-alert v-if="otpSecret" color="info" class="mt-4" variant="tonal" density="compact">
              <p class="mb-1"><strong>Tajný klíč pro ruční zadání:</strong></p>
              <p class="font-weight-bold text-break mb-0 user-select-all">{{ otpSecret }}</p>
              
              <v-btn
                color="primary"
                size="x-small"
                density="comfortable"
                class="mt-2"
                @click="copyToClipboard(otpSecret)"
              >
                Zkopírovat klíč
              </v-btn>
            </v-alert>
            
            <!-- Vstupní pole pro zadání kódu -->
            <v-text-field
              v-model="form.code"
              label="Ověřovací kód"
              type="text"
              class="mt-4"
              placeholder="Zadejte 6místný kód z vaší autentifikační aplikace"
              :disabled="isUpdating"
              required
              variant="outlined"
              :error-messages="form.errors.code"
            ></v-text-field>
            
            <p class="text-caption mt-2">
              Zadejte ověřovací kód vygenerovaný vaší autentifikační aplikací pro dokončení aktivace.
            </p>
          </div>
          
          <div v-else class="text-center my-4">
            <v-alert color="error" variant="tonal">
              Nepodařilo se vygenerovat QR kód. Zkuste to prosím znovu.
            </v-alert>
          </div>
        </div>
      </v-card-text>
      
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" text @click="dialogModel = false" :disabled="isUpdating">
          Zrušit
        </v-btn>
        <v-btn 
          :color="twoFactorEnabled ? 'error' : 'primary'" 
          @click="confirm" 
          :loading="isUpdating"
          :disabled="!twoFactorEnabled && (!qrCode || !form.code)">
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
import QRCode from 'qrcode'

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
    console.log('Dialog otevřen, hodnota twoFactorEnabled:', twoFactorEnabled.value)
    generateQrCode()
  } else {
    // Dialog se zavírá - resetujeme formulář
    form.reset()
    if (!twoFactorEnabled.value) {
      qrCode.value = ''
      otpSecret.value = ''
    }
  }
})

const generateQrCode = () => {
  if (!twoFactorEnabled.value) {
    isLoading.value = true
    otpSecret.value = ''
    
    axios.get(route('two-factor.qr-code'))
      .then(response => {
        if (response.data && response.data.qr_code) {
          // Zpracování OTP Auth URL
          const otpAuthUrl = response.data.qr_code
          console.log('OTP Auth URL:', otpAuthUrl)
          
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
              console.log('QR kód vygenerován lokálně')
            })
            .catch(err => {
              console.error('Chyba při generování QR kódu:', err)
              // Použití otpAuthUrl jako záloha
              qrCode.value = otpAuthUrl
            })
          
        } else if (response.data && response.data.svg) {
          // Zpětná kompatibilita pro SVG formát
          qrCode.value = response.data.svg
          console.log('SVG formát QR kódu získán')
        } else {
          console.error('Neplatný formát QR kódu v odpovědi API:', response.data)
          emit('error', 'Nepodařilo se vygenerovat QR kód: Neplatný formát odpovědi')
        }
        
        isLoading.value = false
      })
      .catch(error => {
        console.error('Chyba při generování QR kódu:', error)
        emit('error', 'Nepodařilo se vygenerovat QR kód: ' + (error.response?.data?.message || error.message))
        isLoading.value = false
      })
  }
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
      // Ověříme kód pomocí správného endpointu z controlleru
      axios.post(route('two-factor.enable'), { code: form.code })
        .then(() => {
          // Po úspěšném ověření kódu oznámíme uživateli, že 2FA bylo aktivováno
          // a aktualizujeme store
          dialogModel.value = false
          emit('success', 'Dvoufaktorové ověření bylo úspěšně aktivováno')
          form.reset()
          // Aktualizujeme nastavení v userStore
          userStore.fetchParameters().finally(() => {
            isUpdating.value = false
          })
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
    axios.delete(route('two-factor.disable'))
      .then(() => {
        dialogModel.value = false
        emit('success', 'Dvoufaktorové ověření bylo úspěšně deaktivováno')
        // Aktualizujeme nastavení v userStore
        userStore.fetchParameters().finally(() => {
          isUpdating.value = false
        })
      })
      .catch(error => {
        isUpdating.value = false
        emit('error', 'Nepodařilo se deaktivovat dvoufaktorové ověření: ' + (error.response?.data?.message || error.message))
      })
  }
}

const copyToClipboard = (text) => {
  const input = document.createElement('input')
  input.value = text
  document.body.appendChild(input)
  input.select()
  document.execCommand('copy')
  document.body.removeChild(input)
}
</script> 