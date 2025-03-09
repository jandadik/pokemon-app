<template>
    <v-container class="fill-height">
        <v-row justify="center" align="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12 pa-8">
                    <v-card-title class="text-h4 text-center mb-4">
                        {{ $t('auth.login.title') }}
                    </v-card-title>

                    <v-card-text>
                        <v-alert
                            v-if="status"
                            type="success"
                            class="mb-4"
                        >
                            {{ status }}
                        </v-alert>

                        <v-form 
                            ref="formRef"
                            @submit.prevent="submit" 
                            v-model="isFormValid"
                        >
                            <v-text-field
                                v-model="form.email"
                                :label="$t('auth.login.email')"
                                type="email"
                                required
                                :rules="emailRules"
                                :error-messages="errors.email"
                                @update:model-value="() => errors.email = ''"
                                prepend-inner-icon="mdi-email"
                            />

                            <v-text-field
                                v-model="form.password"
                                :label="$t('auth.login.password')"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                :rules="passwordRules"
                                :error-messages="errors.password"
                                @update:model-value="() => errors.password = 'hovnohovno'"
                                prepend-inner-icon="mdi-lock"
                                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                @click:append-inner="showPassword = !showPassword"
                            />

                            <div class="d-flex align-center mb-4">
                                <v-checkbox
                                    v-model="form.remember"
                                    :label="$t('auth.login.remember')"
                                    hide-details
                                    class="mt-0"
                                />
                            </div>

                            <v-btn
                                color="primary"
                                type="submit"
                                block
                                :loading="form.processing"
                                :disabled="!isFormValid || form.processing"
                            >
                                {{ $t('auth.login.submit') }}
                            </v-btn>

                            <div class="d-flex align-items-center my-4">
                                <v-divider class="flex-grow-1"></v-divider>
                                <span class="mx-4 text-medium-emphasis">{{ $t('auth.login.or') }}</span>
                                <v-divider class="flex-grow-1"></v-divider>
                            </div>
                            
                            <v-btn
                                color="red darken-1"
                                block
                                @click="redirectToWorkOS"
                                class="mb-4"
                                prepend-icon="mdi-google"
                            >
                                {{ $t('auth.login.google') }}
                            </v-btn>

                            <div class="text-center mt-4">
                                <span class="text-medium-emphasis">{{ $t('auth.login.no_account') }}</span>
                                <v-btn
                                    variant="text"
                                    color="primary"
                                    @click="navigateToRegister"
                                    class="text-none ms-2"
                                >
                                    {{ $t('auth.login.register') }}
                                </v-btn>
                            </div>
                            
                            <div class="text-center mt-2">
                                <v-btn
                                    variant="text"
                                    color="secondary"
                                    @click="navigateToForgotPassword"
                                    class="text-none"
                                >
                                    {{ $t('auth.login.forgot') }}
                                </v-btn>
                            </div>
                        </v-form>
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
    status: {
        type: String,
        default: null
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    canResetPassword: {
        type: Boolean,
        default: false
    }
})

const formRef = ref(null)
const showPassword = ref(false)
const isFormValid = ref(true)

// Funkce pro získání validačních textů
const validationText = {
  emailRequired: 'E-mail je povinný',
  emailValid: 'E-mail musí být platný',
  passwordRequired: 'Heslo je povinné',
  passwordMinLength: 'Heslo musí mít alespoň 8 znaků'
}

const emailRules = [
    v => !!v || validationText.emailRequired,
    v => /.+@.+\..+/.test(v) || validationText.emailValid,
]

const passwordRules = [
    v => !!v || validationText.passwordRequired,
    v => v?.length >= 8 || validationText.passwordMinLength,
]

const form = useForm({
    email: 'janda@janda4.cz',
    password: 'hovnohovno',
    remember: false
})

const submit = async () => {
    if (formRef.value) {
        const { valid } = await formRef.value.validate()
        if (!valid) {
            isFormValid.value = false
            return
        }
    }

    form.post(route('auth.login.store'), {
        onSuccess: () => {
            isFormValid.value = true
            form.reset()
        },
        onError: () => {
            isFormValid.value = false
        }
    })
}

const navigateToForgotPassword = () => {
    router.visit(route('auth.password.request'))
}

const navigateToRegister = () => {
    router.visit(route('auth.user-account.create'))
}

const redirectToWorkOS = () => {
    // Místo fetch požadavku použijeme přímé otevření v novém okně nebo záložce
    window.open(route('auth.workos'), '_self');
}
</script>