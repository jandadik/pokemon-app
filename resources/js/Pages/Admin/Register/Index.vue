<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <h2>Správa číselníků</h2>
            <v-btn 
              color="primary" 
              prepend-icon="mdi-plus" 
              @click="openDialog()"
              v-if="auth.can('register.create')"
            >
              Nový číselník
            </v-btn>
          </v-card-title>
          
          <v-card-text>
            <v-text-field
              v-model="search"
              prepend-icon="mdi-magnify"
              label="Hledat číselník"
              single-line
              hide-details
              class="mb-4"
            ></v-text-field>
            
            <v-data-table
              :headers="headers"
              :items="categories"
              :search="search"
              :items-per-page="15"
              class="elevation-1"
            >
              <template #[`item.actions`]="{ item }">
                <v-btn
                  icon
                  variant="text"
                  color="info"
                  @click="navigateTo(route('admin.registers.index', item.id))"
                  v-if="auth.can('register.view')"
                >
                  <v-icon>mdi-format-list-bulleted</v-icon>
                </v-btn>
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
    
    <!-- Dialog pro vytvoření/editaci číselníku -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          {{ isEditing ? 'Upravit číselník' : 'Nový číselník' }}
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
            {{ isEditing ? 'Uložit změny' : 'Vytvořit číselník' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    
    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title>Smazat číselník</v-card-title>
        <v-card-text>
          Opravdu chcete smazat číselník <strong>{{ categoryToDelete?.name }}</strong>?
          <div v-if="categoryToDelete?.registers_count > 0" class="text-red mt-2">
            Tento číselník obsahuje {{ categoryToDelete.registers_count }} položek a nelze jej smazat.
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="deleteDialog = false">Zrušit</v-btn>
          <v-btn 
            color="error" 
            @click="deleteCategory"
            :disabled="categoryToDelete?.registers_count > 0"
          >
            Smazat
          </v-btn>
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
const categoryToDelete = ref(null)

const props = defineProps({
  categories: {
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
  { title: 'Počet položek', key: 'registers_count' },
  { title: 'Akce', key: 'actions', sortable: false }
]

const form = useForm({
  name: '',
  type: ''
})

const isEditing = computed(() => !!form.id)

const openDialog = (category = null) => {
  if (category) {
    form.id = category.id
    form.name = category.name
    form.type = category.type
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
    form.put(route('admin.register-categories.update', form.id), {
      onSuccess: () => {
        dialog.value = false
        form.reset()
      },
      onError: () => {
        isFormValid.value = false
      }
    })
  } else {
    form.post(route('admin.register-categories.store'), {
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

const confirmDelete = (category) => {
  categoryToDelete.value = category
  deleteDialog.value = true
}

const deleteCategory = () => {
  router.delete(route('admin.register-categories.destroy', categoryToDelete.value.id), {
    onSuccess: () => {
      deleteDialog.value = false
      categoryToDelete.value = null
    }
  })
}

const navigateTo = (url) => {
  router.visit(url)
}
</script> 