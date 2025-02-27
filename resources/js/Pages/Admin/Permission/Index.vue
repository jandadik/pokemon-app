<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <h2>Správa oprávnění</h2>
            <v-btn 
              color="primary" 
              prepend-icon="mdi-plus" 
              @click="navigateTo(route('admin.permissions.create'))"
              v-if="auth.can('permission.create')"
            >
              Nové oprávnění
            </v-btn>
          </v-card-title>
          
          <v-card-text>
            <v-tabs v-model="activeTab">
              <v-tab value="all">Všechna oprávnění</v-tab>
              <v-tab value="grouped">Podle modulů</v-tab>
            </v-tabs>
            
            <v-window v-model="activeTab" class="mt-4">
              <!-- Všechna oprávnění -->
              <v-window-item value="all">
                <v-text-field
                  v-model="search"
                  prepend-icon="mdi-magnify"
                  label="Hledat oprávnění"
                  single-line
                  hide-details
                  class="mb-4"
                ></v-text-field>
                
                <v-data-table
                  :headers="headers"
                  :items="permissions"
                  :search="search"
                  :items-per-page="15"
                  class="elevation-1"
                >
                  <template #[`item.actions`]="{ item }">
                    <v-btn
                      icon
                      variant="text"
                      color="primary"
                      @click="navigateTo(route('admin.permissions.edit', item.id))"
                      v-if="auth.can('permission.edit')"
                    >
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                      icon
                      variant="text"
                      color="error"
                      @click="confirmDelete(item)"
                      v-if="auth.can('permission.delete') && item.roles_count === 0"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </template>
                </v-data-table>
              </v-window-item>
              
              <!-- Seskupená oprávnění -->
              <v-window-item value="grouped">
                <v-expansion-panels>
                  <v-expansion-panel
                    v-for="(modulePermissions, module) in groupedPermissions"
                    :key="module"
                  >
                    <v-expansion-panel-title>
                      <div class="d-flex align-center">
                        <v-icon class="mr-2">mdi-folder</v-icon>
                        <span class="text-capitalize">{{ module }}</span>
                        <v-chip class="ml-2" size="small">{{ modulePermissions.length }}</v-chip>
                      </div>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                      <v-list lines="two">
                        <v-list-item
                          v-for="permission in modulePermissions"
                          :key="permission.id"
                          :title="permission.name"
                          :subtitle="`Používáno v ${permission.roles_count} rolích`"
                        >
                          <template #append>
                            <v-btn
                              icon
                              variant="text"
                              color="primary"
                              @click="navigateTo(route('admin.permissions.edit', permission.id))"
                              v-if="auth.can('permission.edit')"
                            >
                              <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                              icon
                              variant="text"
                              color="error"
                              @click="confirmDelete(permission)"
                              v-if="auth.can('permission.delete') && permission.roles_count === 0"
                            >
                              <v-icon>mdi-delete</v-icon>
                            </v-btn>
                          </template>
                        </v-list-item>
                      </v-list>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-window-item>
            </v-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title>Smazat oprávnění</v-card-title>
        <v-card-text>
          Opravdu chcete smazat oprávnění <strong>{{ permissionToDelete?.name }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="deleteDialog = false">Zrušit</v-btn>
          <v-btn color="error" @click="deletePermission">Smazat</v-btn>
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
const activeTab = ref('all')
const search = ref('')

const props = defineProps({
  permissions: {
    type: Array,
    required: true
  },
  groupedPermissions: {
    type: Object,
    required: true
  }
})

const headers = [
  { title: 'Název', key: 'name' },
  { title: 'Modul', key: 'module' },
  { title: 'Akce', key: 'action' },
  { title: 'Počet rolí', key: 'roles_count' },
  { title: 'Akce', key: 'actions', sortable: false }
]

const deleteDialog = ref(false)
const permissionToDelete = ref(null)

const navigateTo = (url) => {
  router.visit(url)
}

const confirmDelete = (permission) => {
  permissionToDelete.value = permission
  deleteDialog.value = true
}

const deletePermission = () => {
  router.delete(route('admin.permissions.destroy', permissionToDelete.value.id), {
    onSuccess: () => {
      deleteDialog.value = false
      permissionToDelete.value = null
    }
  })
}
</script> 