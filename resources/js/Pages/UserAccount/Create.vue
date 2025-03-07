<template>
    <v-container class="fill-height">
        <v-row justify="center" align="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12 pa-8">
                    <v-card-title class="text-h4 text-center mb-4">
                        Registrace
                    </v-card-title>

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

                    <v-form ref="formRef" @submit.prevent="submit" v-model="isFormValid">
                        <v-text-field
                            v-model="form.name"
                            label="Jméno"
                            type="text"
                            prepend-icon="mdi-account"
                            required
                            :error-messages="errors.name"
                            :rules="[v => !!v || 'Jméno je povinné']"
                            @update:model-value="() => handleFieldUpdate('name')"
                        />

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
                            :rules="passwordRules"
                            @update:model-value="() => handleFieldUpdate('password')"
                        />

                        <v-text-field
                            v-model="form.password_confirmation"
                            label="Potvrzení hesla"
                            :type="showPassword ? 'text' : 'password'"
                            prepend-icon="mdi-lock"
                            required
                            :error-messages="errors.password_confirmation"
                            :rules="[
                                v => !!v || 'Potvrzení hesla je povinné',
                                v => v === form.password || 'Hesla se neshodují'
                            ]"
                            @update:model-value="() => handleFieldUpdate('password_confirmation')"
                        />

                        <v-btn
                            type="submit"
                            color="primary"
                            block
                            class="mt-4"
                            :loading="form.processing"
                            :disabled="!isFormValid"
                        >
                            Registrovat
                        </v-btn>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, nextTick } from 'vue'
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
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const emailRules = [
    v => !!v || 'Email je povinný',
    v => /.+@.+\..+/.test(v) || 'Zadejte platnou emailovou adresu'
]

const passwordRules = [
    v => !!v || 'Heslo je povinné',
    v => v?.length >= 8 || 'Heslo musí mít alespoň 8 znaků'
]

const handleFieldUpdate = async (field) => {
    if (props.errors[field]) {
        delete props.errors[field]
    }
    
    if (formRef.value) {
        await nextTick()
        formRef.value.validate()
    }
}

const submit = () => {
    if (!isFormValid.value) return

    form.post(route('auth.user-account.store'), {
        onFinish: () => {
            if (!Object.keys(props.errors).length) {
                form.reset()
            }
        },
    })
}
</script>