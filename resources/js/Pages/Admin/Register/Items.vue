<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <div>
              <h2>{{ category.name }}</h2>
              <div class="text-subtitle-1 text-grey">{{ category.type }}</div>
            </div>
            <div class="d-flex gap-2">
              <v-btn 
                color="secondary" 
                variant="text"
                prepend-icon="mdi-arrow-left" 
                @click="navigateTo(route('admin.register-categories.index'))"
              >
                Zpět na seznam
              </v-btn>
              <v-btn 
                color="primary" 
                prepend-icon="mdi-plus" 
                @click="openDialog()"
                v-if="auth.can('register.create')"
              >
                Nová položka
              </v-btn>
            </div>
          </v-card-title>
          
          <v-card-text>
            <v-text-field
              v-model="search"
              prepend-icon="mdi-magnify"
              label="Hledat položku"
              single-line
              hide-details
              class="mb-4"
            ></v-text-field>
            
            <v-data-table
              :headers="headers"
              :items="registers"
              :search="search"
              :items-per-page="15"
              class="elevation-1"
            >
              <template #[`item.default`]="{ item }">
                <v-icon :color="item.default ? 'success' : 'grey'">
                  {{ item.default ? 'mdi-check-circle' : 'mdi-circle-outline' }}
                </v-icon>
              </template>

              <template #[`item.actions`]="{ item }">
                <v-btn
                  icon
                  variant="text"
                  color="primary"
                  @click="openDialog(item)"
                  v-if="auth.can('register.edit')"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  variant="text"
                  color="error"
                  @click="confirmDelete(item)"
                  v-if="auth.can('register.delete')"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <!-- Dialog pro vytvoření/editaci položky -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          {{ isEditing ? 'Upravit položku' : 'Nová položka' }}
        </v-card-title>
        
        <v-card-text>
          <v-form @submit.prevent="submit" ref="formRef" v-model="isFormValid">
            <v-text-field
              v-model="form.name"
              label="Název"
              required
              :error-messages="errors.name"
              @update:model-value="() => handleFieldUpdate('name')"
            ></v-text-field>
            
            <v-text-field
              v-model="form.type"
              label="Typ"
              required
              :error-messages="errors.type"
              @update:model-value="() => handleFieldUpdate('type')"
              hint="Identifikátor pro programové zpracování"
              persistent-hint
            ></v-text-field>

            <v-switch
              v-model="form.default"
              label="Výchozí hodnota"
              color="primary"
              hide-details
              class="mt-4"
            ></v-switch>
          </v-form>
        </v-card-text>
        
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="dialog = false">Zrušit</v-btn>
          <v-btn 
            color="primary" 
            @click="submit"
            :loading="form.processing"
            :disabled="!isFormValid || form.processing"
          >
            {{ isEditing ? 'Uložit změny' : 'Vytvořit položku' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    
    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title>Smazat položku</v-card-title>
        <v-card-text>
          Opravdu chcete smazat položku <strong>{{ registerToDelete?.name }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="deleteDialog = false">Zrušit</v-btn>
          <v-btn color="error" @click="deleteRegister">Smazat</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const search = ref('')
const dialog = ref(false)
const deleteDialog = ref(false)
const formRef = ref(null)
const isFormValid = ref(false)
const registerToDelete = ref(null)

const props = defineProps({
  category: {
    type: Object,
    required: true
  },
  registers: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const headers = [
  { title: 'Název', key: 'name' },
  { title: 'Typ', key: 'type' },
  { title: 'Výchozí', key: 'default', align: 'center' },
  { title: 'Akce', key: 'actions', sortable: false }
]

const form = useForm({
  name: '',
  type: '',
  default: false
})

const isEditing = computed(() => !!form.id)

const openDialog = (register = null) => {
  if (register) {
    form.id = register.id
    form.name = register.name
    form.type = register.type
    form.default = register.default
  } else {
    form.reset()
    form.clearErrors()
  }
  dialog.value = true
}

const handleFieldUpdate = async (field) => {
  if (props.errors[field]) {
    delete props.errors[field]
  }
  
  if (formRef.value) {
    await nextTick()
    await formRef.value.validate()
  }
}

const submit = () => {
  if (isEditing.value) {
    form.put(route('admin.registers.update', [props.category.id, form.id]), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      },
      onError: () => {
        isFormValid.value = false
      }
    })
  } else {
    form.post(route('admin.registers.store', props.category.id), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      },
      onError: () => {
        isFormValid.value = false
      }
    })
  }
}

const confirmDelete = (register) => {
  registerToDelete.value = register
  deleteDialog.value = true
}

const deleteRegister = () => {
  router.delete(route('admin.registers.destroy', [props.category.id, registerToDelete.value.id]), {
    onSuccess: () => {
      deleteDialog.value = false
      registerToDelete.value = null
    }
  })
}

const navigateTo = (url) => {
  router.visit(url)
}
</script> 