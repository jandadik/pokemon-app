<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="8" offset-md="2">
        <v-card>
          <v-card-title>
            <h2>Nový uživatel</h2>
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
              
              <v-text-field
                v-model="form.password"
                label="Heslo"
                type="password"
                required
                :error-messages="errors.password"
                @update:model-value="() => handleFieldUpdate('password')"
                hint="Heslo musí obsahovat minimálně 8 znaků"
                persistent-hint
              ></v-text-field>
              
              <v-text-field
                v-model="form.password_confirmation"
                label="Potvrzení hesla"
                type="password"
                required
                :error-messages="errors.password_confirmation"
                @update:model-value="() => handleFieldUpdate('password_confirmation')"
              ></v-text-field>
              
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
              ></v-select>
              
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
                  Vytvořit uživatele
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
import { ref, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
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

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: []
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
  form.post(route('admin.users.store'))
}
</script> 