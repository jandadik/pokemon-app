<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="8" offset-md="2">
        <v-card>
          <v-card-title>
            <h2>Upravit oprávnění</h2>
          </v-card-title>
          
          <v-card-text>
            <v-form @submit.prevent="submit" ref="formRef" v-model="isFormValid">
              <v-row>
                <v-col cols="12" md="6">
                  <v-autocomplete
                    v-model="form.module"
                    :items="modules"
                    label="Modul"
                    required
                    :error-messages="errors.module"
                    @update:model-value="() => handleFieldUpdate('module')"
                    hint="Vyberte existující modul nebo zadejte nový"
                    persistent-hint
                    clearable
                  ></v-autocomplete>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-autocomplete
                    v-model="form.action"
                    :items="commonActions"
                    label="Akce"
                    required
                    :error-messages="errors.action"
                    @update:model-value="() => handleFieldUpdate('action')"
                    hint="Vyberte existující akci nebo zadejte novou"
                    persistent-hint
                    clearable
                  ></v-autocomplete>
                </v-col>
              </v-row>
              
              <v-alert
                type="info"
                variant="tonal"
                class="mt-4"
              >
                Výsledné oprávnění: <strong>{{ permissionPreview }}</strong>
              </v-alert>
              
              <v-alert
                v-if="permission.roles_count > 0"
                type="warning"
                variant="tonal"
                class="mt-4"
              >
                Toto oprávnění je používáno v {{ permission.roles_count }} rolích. 
                Změna názvu oprávnění se projeví ve všech rolích.
              </v-alert>
              
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                  color="primary" 
                  variant="text" 
                  @click="navigateTo(route('admin.permissions.index'))"
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
import { ref, computed, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  permission: {
    type: Object,
    required: true
  },
  modules: {
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
  module: props.permission.module,
  action: props.permission.action
})

const commonActions = [
  'view',
  'create',
  'edit',
  'delete',
  'access'
]

const permissionPreview = computed(() => {
  if (!form.module || !form.action) return '...';
  return `${form.module}.${form.action}`;
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
  form.put(route('admin.permissions.update', props.permission.id))
}
</script> 