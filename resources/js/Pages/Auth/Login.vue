<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="card p-8 shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-6 text-text-primary dark:text-text-primary-dark">
          {{ $t('auth.login.title') }}
        </h1>

        <div
          v-if="status"
          class="mb-4 p-4 rounded-lg bg-success/10 text-success border border-success/20"
        >
          {{ status }}
        </div>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="label">{{ $t('auth.login.email') }}</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 mdi mdi-email text-text-secondary"></span>
              <InputText
                v-model="form.email"
                type="email"
                :class="[
                  'input pl-10',
                  errors.email ? 'input-error' : ''
                ]"
                :pt="{
                  root: 'w-full'
                }"
              />
            </div>
            <p v-if="errors.email" class="error-text">{{ errors.email }}</p>
          </div>

          <div class="mb-4">
            <label class="label">{{ $t('auth.login.password') }}</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 mdi mdi-lock text-text-secondary"></span>
              <InputText
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                :class="[
                  'input pl-10 pr-10',
                  errors.password ? 'input-error' : ''
                ]"
                :pt="{
                  root: 'w-full'
                }"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary"
                @click="showPassword = !showPassword"
              >
                <span :class="['mdi', showPassword ? 'mdi-eye' : 'mdi-eye-off']"></span>
              </button>
            </div>
            <p v-if="errors.password" class="error-text">{{ errors.password }}</p>
          </div>

          <div class="flex items-center mb-4">
            <Checkbox
              v-model="form.remember"
              :binary="true"
              inputId="remember"
              :pt="{
                root: 'w-5 h-5',
                box: 'border border-input-border dark:border-input-border-dark rounded'
              }"
            />
            <label for="remember" class="ml-2 text-sm text-text-primary dark:text-text-primary-dark">
              {{ $t('auth.login.remember') }}
            </label>
          </div>

          <Button
            type="submit"
            :loading="form.processing"
            :disabled="form.processing"
            class="btn-primary w-full justify-center"
            :pt="{
              root: 'w-full',
              label: 'font-medium'
            }"
          >
            {{ $t('auth.login.submit') }}
          </Button>

          <div class="flex items-center my-4">
            <div class="flex-grow border-t border-border dark:border-border-dark"></div>
            <span class="mx-4 text-text-secondary text-sm">{{ $t('auth.login.or') }}</span>
            <div class="flex-grow border-t border-border dark:border-border-dark"></div>
          </div>

          <button
            type="button"
            class="btn w-full justify-center bg-red-600 text-white hover:bg-red-700 mb-4"
            @click="redirectToWorkOS"
          >
            <span class="mdi mdi-google mr-2"></span>
            {{ $t('auth.login.google') }}
          </button>

          <div class="text-center mt-4">
            <span class="text-text-secondary text-sm">{{ $t('auth.login.no_account') }}</span>
            <button
              type="button"
              class="btn-text text-primary ml-2"
              @click="navigateToRegister"
            >
              {{ $t('auth.login.register') }}
            </button>
          </div>

          <div class="text-center mt-2">
            <button
              type="button"
              class="btn-text text-secondary"
              @click="navigateToForgotPassword"
            >
              {{ $t('auth.login.forgot') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Checkbox from 'primevue/checkbox'
import Button from 'primevue/button'

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

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post(route('auth.login.store'), {
    onSuccess: () => {
      form.reset()
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
  window.open(route('auth.workos'), '_self')
}
</script>
