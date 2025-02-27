<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="8" offset-md="2">
        <v-card>
          <v-card-title>
            <h2>{{ isEditing ? 'Upravit roli' : 'Nová role' }}</h2>
          </v-card-title>
          
          <v-card-text>
            <v-form @submit.prevent="submit" ref="formRef" v-model="isFormValid">
              <v-text-field
                v-model="form.name"
                label="Název role"
                required
                :disabled="role && ['super-admin', 'admin'].includes(role.name)"
                :error-messages="errors.name"
                @update:model-value="() => handleFieldUpdate('name')"
              ></v-text-field>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="text-h6 mb-4">Oprávnění</div>
              
              <v-row>
                <v-col 
                  v-for="(permissionGroup, groupName) in groupedPermissions" 
                  :key="groupName"
                  cols="12" 
                  md="6"
                >
                  <v-card variant="outlined" class="mb-4">
                    <v-card-title class="text-subtitle-1">
                      {{ formatGroupName(groupName) }}
                      <v-checkbox
                        v-model="selectAllGroups[groupName]"
                        label="Vybrat vše"
                        hide-details
                        density="compact"
                        class="ml-2"
                        @change="toggleGroupPermissions(groupName)"
                      ></v-checkbox>
                    </v-card-title>
                    
                    <v-card-text>
                      <v-checkbox
                        v-for="permission in permissionGroup"
                        :key="permission.id"
                        v-model="form.permissions"
                        :value="permission.id"
                        :label="formatPermissionName(permission.name)"
                        hide-details
                        density="compact"
                        class="mb-1"
                        :disabled="role && role.name === 'super-admin'"
                      ></v-checkbox>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
              
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn 
                  color="primary" 
                  variant="text" 
                  @click="navigateTo(route('admin.roles.index'))"
                >
                  Zrušit
                </v-btn>
                <v-btn 
                  color="primary" 
                  type="submit" 
                  :loading="form.processing"
                  :disabled="!isFormValid || form.processing"
                >
                  {{ isEditing ? 'Uložit změny' : 'Vytvořit roli' }}
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
import { ref, computed, onMounted, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'
import { router } from '@inertiajs/vue3'

const auth = useAuthStore()
const formRef = ref(null)
const isFormValid = ref(true)
const selectAllGroups = ref({})

const props = defineProps({
  role: {
    type: Object,
    default: null
  },
  permissions: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const isEditing = computed(() => !!props.role)

const form = useForm({
  name: props.role?.name || '',
  permissions: props.role?.permissions.map(p => p.id) || []
})

const groupedPermissions = computed(() => {
  const groups = {}
  
  props.permissions.forEach(permission => {
    const groupName = permission.name.split('.')[0]
    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(permission)
  })
  
  return groups
})

// Inicializace selectAllGroups
onMounted(() => {
  Object.keys(groupedPermissions.value).forEach(groupName => {
    const groupPermissions = groupedPermissions.value[groupName].map(p => p.id)
    const selectedCount = groupPermissions.filter(id => form.permissions.includes(id)).length
    selectAllGroups.value[groupName] = selectedCount === groupPermissions.length
  })
})

const formatGroupName = (name) => {
  return name.charAt(0).toUpperCase() + name.slice(1)
}

const formatPermissionName = (name) => {
  const parts = name.split('.')
  if (parts.length > 1) {
    const action = parts[1]
    return {
      'view': 'Zobrazit',
      'create': 'Vytvářet',
      'edit': 'Upravovat',
      'delete': 'Mazat',
      'access': 'Přístup'
    }[action] || action
  }
  return name
}

const toggleGroupPermissions = (groupName) => {
  const groupPermissions = groupedPermissions.value[groupName].map(p => p.id)
  
  if (selectAllGroups.value[groupName]) {
    // Přidat všechna oprávnění ze skupiny
    groupPermissions.forEach(id => {
      if (!form.permissions.includes(id)) {
        form.permissions.push(id)
      }
    })
  } else {
    // Odebrat všechna oprávnění ze skupiny
    form.permissions = form.permissions.filter(id => !groupPermissions.includes(id))
  }
}

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
  if (isEditing.value) {
    form.put(route('admin.roles.update', props.role.id))
  } else {
    form.post(route('admin.roles.store'))
  }
}

const navigateTo = (url) => {
  router.visit(url)
}
</script> 