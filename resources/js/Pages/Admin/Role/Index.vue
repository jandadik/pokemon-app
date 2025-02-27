<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <h2>Správa rolí</h2>
            <v-btn 
              color="primary" 
              prepend-icon="mdi-plus" 
              @click="navigateTo(route('admin.roles.create'))"
              v-if="auth.can('role.create')"
            >
              Nová role
            </v-btn>
          </v-card-title>
          
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="roles"
              :items-per-page="10"
              class="elevation-1"
            >
              <template #[`item.actions`]="{ item }">
                <v-btn
                  icon
                  variant="text"
                  color="primary"
                  @click="navigateTo(route('admin.roles.edit', item.id))"
                  v-if="auth.can('role.edit')"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  variant="text"
                  color="error"
                  @click="confirmDelete(item)"
                  v-if="auth.can('role.delete') && item.name !== 'super-admin'"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              
              <template #[`item.permissions`]="{ item }">
                <v-chip
                  v-for="permission in item.permissions.slice(0, 3)"
                  :key="permission.id"
                  class="ma-1"
                  size="small"
                >
                  {{ permission.name }}
                </v-chip>
                <v-chip
                  v-if="item.permissions.length > 3"
                  size="small"
                  class="ma-1"
                >
                  +{{ item.permissions.length - 3 }}
                </v-chip>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title>Smazat roli</v-card-title>
        <v-card-text>
          Opravdu chcete smazat roli <strong>{{ roleToDelete?.name }}</strong>?
          <div class="text-red mt-2">
            Tato akce je nevratná a odebere roli všem uživatelům.
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="deleteDialog = false">Zrušit</v-btn>
          <v-btn color="error" @click="deleteRole">Smazat</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { router } from '@inertiajs/vue3'

const auth = useAuthStore()

const props = defineProps({
  roles: {
    type: Array,
    required: true
  }
})

const headers = [
  { title: 'Název', key: 'name' },
  { title: 'Oprávnění', key: 'permissions' },
  { title: 'Počet uživatelů', key: 'users_count' },
  { title: 'Akce', key: 'actions', sortable: false }
]

const deleteDialog = ref(false)
const roleToDelete = ref(null)

const navigateTo = (url) => {
  router.visit(url)
}

const confirmDelete = (role) => {
  roleToDelete.value = role
  deleteDialog.value = true
}

const deleteRole = () => {
  router.delete(route('admin.roles.destroy', roleToDelete.value.id), {
    onSuccess: () => {
      deleteDialog.value = false
      roleToDelete.value = null
    }
  })
}
</script> 