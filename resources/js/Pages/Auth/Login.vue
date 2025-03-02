<template>
    <v-container class="fill-height">
        <v-row justify="center" align="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12 pa-8">
                    <v-card-title class="text-h4 text-center mb-4">
                        Přihlášení
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
                                label="Email"
                                type="email"
                                required
                                :rules="emailRules"
                                :error-messages="errors.email"
                                @update:model-value="() => errors.email = ''"
                                prepend-inner-icon="mdi-email"
                            />

                            <v-text-field
                                v-model="form.password"
                                label="Heslo"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                :rules="passwordRules"
                                :error-messages="errors.password"
                                @update:model-value="() => errors.password = 'hovnohovno'"
                                prepend-inner-icon="mdi-lock"
                                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                @click:append-inner="showPassword = !showPassword"
                            />

                            <div class="d-flex align-center justify-space-between mb-4">
                                <v-checkbox
                                    v-model="form.remember"
                                    label="Zapamatovat si mě"
                                    hide-details
                                    class="mt-0"
                                />

                                <v-btn
                                    variant="text"
                                    color="primary"
                                    @click="navigateToForgotPassword"
                                    class="text-none"
                                >
                                    Zapomenuté heslo?
                                </v-btn>
                            </div>

                            <v-btn
                                color="primary"
                                type="submit"
                                block
                                :loading="form.processing"
                                :disabled="!isFormValid || form.processing"
                            >
                                Přihlásit se
                            </v-btn>

                            <div class="text-center mt-4">
                                <span class="text-medium-emphasis">Nemáte účet?</span>
                                <v-btn
                                    variant="text"
                                    color="primary"
                                    @click="navigateToRegister"
                                    class="text-none ms-2"
                                >
                                    Vytvořit účet
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

const emailRules = [
    v => !!v || 'E-mail je povinný',
    v => /.+@.+\..+/.test(v) || 'E-mail musí být platný',
]

const passwordRules = [
    v => !!v || 'Heslo je povinné',
    v => v?.length >= 8 || 'Heslo musí mít alespoň 8 znaků',
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

    form.post(route('login.store'), {
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
    router.visit(route('password.request'))
}

const navigateToRegister = () => {
    router.visit(route('user-account.create'))
}
</script>