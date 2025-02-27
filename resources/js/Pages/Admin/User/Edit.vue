<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="8" offset-md="2">
        <v-card>
          <v-card-title>
            <h2>Upravit uživatele</h2>
          </v-card-title>
          
          <v-card-text>
            <v-form @submit.prevent="submit" ref="formRef" v-model="isFormValid">
              <v-text-field
                v-model="form.name"
                label="Jméno"
                required
                :error-messages="errors.name"
                @update:model-value="() => handleFieldUpdate('name')"
              ></v-text-field>
              
              <v-text-field
                v-model="form.email"
                label="Email"
                type="email"
                required
                :error-messages="errors.email"
                @update:model-value="() => handleFieldUpdate('email')"
              ></v-text-field>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="d-flex align-center mb-4">
                <div class="text-h6">Změna hesla</div>
                <v-switch
                  v-model="changePassword"
                  label="Změnit heslo"
                  hide-details
                  class="ml-4"
                ></v-switch>
              </div>
              
              <v-expand-transition>
                <div v-if="changePassword">
                  <v-text-field
                    v-model="form.password"
                    label="Nové heslo"
                    type="password"
                    :error-messages="errors.password"
                    @update:model-value="() => handleFieldUpdate('password')"
                    hint="Heslo musí obsahovat minimálně 8 znaků"
                    persistent-hint
                  ></v-text-field>
                  
                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Potvrzení nového hesla"
                    type="password"
                    :error-messages="errors.password_confirmation"
                    @update:model-value="() => handleFieldUpdate('password_confirmation')"
                  ></v-text-field>
                </div>
              </v-expand-transition>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="text-h6 mb-4">Role uživatele</div>
              
              <v-select
                v-model="form.roles"
                :items="roles"
                item-title="name"
                item-value="id"
                label="Role"
                multiple
                chips
                :error-messages="errors.roles"
                @update:model-value="() => handleFieldUpdate('roles')"
                :disabled="isRoleSelectDisabled"
              ></v-select>
              
              <v-alert
                v-if="isRoleSelectDisabled"
                type="warning"
                variant="tonal"
                class="mt-4"
              >
                Nemáte oprávnění měnit role administrátorů.
              </v-alert>
              
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                  color="primary" 
                  variant="text" 
                  @click="navigateTo(route('admin.users.index'))"
                >
                  Zrušit
                </v-btn>
                <v-btn 
                  color="primary" 
                  type="submit" 
                  :loading="form.processing"
                  :disabled="!isFormValid || form.processing"
                >
                  Uložit změny
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const page = usePage()

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  roles: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const formRef = ref(null)
const isFormValid = ref(false)
const changePassword = ref(false)

// Bezpečné získání rolí přihlášeného uživatele
const currentUserRoles = computed(() => {
  return page.props.auth?.user?.roles || []
})

// Kontrola, zda je přihlášený uživatel super-admin
const isSuperAdmin = computed(() => {
  return currentUserRoles.value.includes('super-admin')
})

// Kontrola, zda je upravovaný uživatel admin
const isAdminUser = computed(() => {
  return props.user.is_admin
})

// Kontrola, zda má být select pro role zakázán
const isRoleSelectDisabled = computed(() => {
  return isAdminUser.value && !isSuperAdmin.value
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: props.user.roles
})

const handleFieldUpdate = async (field) => {
  if (props.errors[field]) {
    delete props.errors[field]
  }
  
  if (formRef.value) {
    await nextTick()
    formRef.value.validate()
  }
}

const navigateTo = (url) => {
  router.visit(url)
}

const submit = () => {
  // Pokud neměníme heslo, odstraníme ho z formuláře
  if (!changePassword.value) {
    form.password = null
    form.password_confirmation = null
  }
  
  form.put(route('admin.users.update', props.user.id))
}
</script> 