<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center justify-space-between">
            <h2>Správa uživatelů</h2>
            <v-btn 
              color="primary" 
              prepend-icon="mdi-plus" 
              @click="navigateTo(route('admin.users.create'))"
              v-if="auth.can('user.create')"
            >
              Nový uživatel
            </v-btn>
          </v-card-title>
          
          <v-card-text>
            <v-text-field
              v-model="search"
              prepend-icon="mdi-magnify"
              label="Hledat uživatele"
              single-line
              hide-details
              class="mb-4"
            ></v-text-field>
            
            <v-data-table
              :headers="headers"
              :items="users"
              :search="search"
              :items-per-page="15"
              class="elevation-1"
            >
              <template #[`item.roles`]="{ item }">
                <v-chip
                  v-for="role in item.roles"
                  :key="role"
                  class="ma-1"
                  size="small"
                  :color="getRoleColor(role)"
                >
                  {{ role }}
                </v-chip>
              </template>
              
              <template #[`item.actions`]="{ item }">
                <v-btn
                  icon
                  variant="text"
                  color="primary"
                  @click="navigateTo(route('admin.users.edit', item.id))"
                  v-if="auth.can('user.edit')"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  variant="text"
                  color="error"
                  @click="confirmDelete(item)"
                  v-if="auth.can('user.delete') && !item.is_admin && item.id !== currentUserId"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    
    <!-- Dialog pro potvrzení smazání -->
    <v-dialog v-model="deleteDialog" max-width="500px">
      <v-card>
        <v-card-title>Smazat uživatele</v-card-title>
        <v-card-text>
          Opravdu chcete smazat uživatele <strong>{{ userToDelete?.name }}</strong>?
          <div class="text-red mt-2">
            Tato akce je nevratná a odstraní všechna data uživatele.
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="text" @click="deleteDialog = false">Zrušit</v-btn>
          <v-btn color="error" @click="deleteUser">Smazat</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>
  
<script setup>
  import { ref, computed } from 'vue'
  import { useAuthStore } from '@/stores/authStore'
  import { router } from '@inertiajs/vue3'
  
  const auth = useAuthStore()
  const search = ref('')
  
  // Bezpečný přístup k ID přihlášeného uživatele
  const currentUserId = computed(() => {
    return auth.user?.id || null
  })
  
  const props = defineProps({
    users: {
      type: Array,
      required: true
    }
  })
  
  const headers = [
    { title: 'Jméno', key: 'name' },
    { title: 'Email', key: 'email' },
    { title: 'Role', key: 'roles' },
    { title: 'Vytvořen', key: 'created_at' },
    { title: 'Akce', key: 'actions', sortable: false }
  ]
  
  const deleteDialog = ref(false)
  const userToDelete = ref(null)
  
  const navigateTo = (url) => {
    if (url) {
      try {
        router.visit(url, { preserveScroll: true })
      } catch (e) {
        console.error('Chyba při navigaci:', e)
      }
    }
  }
  
  const confirmDelete = (user) => {
    userToDelete.value = user
    deleteDialog.value = true
  }
  
  const deleteUser = () => {
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
      onSuccess: () => {
        deleteDialog.value = false
        userToDelete.value = null
      }
    })
  }
  
  const getRoleColor = (role) => {
    const colors = {
      'super-admin': 'red',
      'admin': 'orange',
      'editor': 'blue',
      'user': 'green'
    }
    
    return colors[role] || 'grey'
  }
</script>