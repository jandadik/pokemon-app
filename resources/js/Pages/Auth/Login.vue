<template>
    <v-container class="fill-height">
        <v-row justify="center" align="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12 pa-8">
                    <v-card-title class="text-h4 text-center mb-4">
                        Přihlášení
                    </v-card-title>

                    <!-- Chybové hlášky -->
                    <v-alert
                        v-if="Object.keys(errors).length > 0"
                        type="error"
                        variant="tonal"
                        class="mb-4"
                    >
                        <ul class="pl-4">
                            <li v-for="(error, index) in errors" :key="index">
                                {{ error }}
                            </li>
                        </ul>
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
                            prepend-icon="mdi-email"
                            required
                            :error-messages="errors.email"
                            :rules="emailRules"
                            @update:model-value="() => handleFieldUpdate('email')"
                        />

                        <v-text-field
                            v-model="form.password"
                            label="Heslo"
                            :type="showPassword ? 'text' : 'password'"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                            required
                            :error-messages="errors.password"
                            :rules="[v => !!v || 'Heslo je povinné']"
                            @update:model-value="() => handleFieldUpdate('password')"
                        />

                        <v-checkbox
                            v-model="form.remember"
                            label="Zapamatovat přihlášení"
                            color="primary"
                            hide-details
                            class="mb-4"
                        />

                        <v-btn
                            type="submit"
                            color="primary"
                            block
                            class="mt-4"
                            :loading="form.processing"
                            :disabled="!isFormValid"
                        >
                            Přihlásit se
                        </v-btn>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
})

const formRef = ref(null)
const showPassword = ref(false)
const isFormValid = ref(false)

const form = useForm({
    email: 'test@example.com',
    password: 'password',
    remember: false
})

const emailRules = [
    v => !!v || 'Email je povinný',
    v => /.+@.+\..+/.test(v) || 'Zadejte platnou emailovou adresu'
]

// Sledujeme změny v errors objektu
watch(() => props.errors, (newErrors) => {
    if (Object.keys(newErrors).length > 0) {
        isFormValid.value = false
    }
}, { deep: true })

const handleFieldUpdate = async (field) => {
    // Vymaže chybu pro konkrétní pole
    if (props.errors[field]) {
        delete props.errors[field]
    }
    
    // Resetuje validaci formuláře
    if (formRef.value) {
        await nextTick()
        formRef.value.validate()
    }
}

const submit = () => {
    if (!isFormValid.value) return

    form.post(route('login.store'), {
        onFinish: () => {
            if (!Object.keys(props.errors).length) {
                form.reset('password')
            }
        },
    })
}
</script>