<template>
    <v-container>
      <v-row>
        <v-col cols="12">
          <h1 class="text-h3 mb-6">Administrace</h1>
        </v-col>
      </v-row>
  
      <v-row>
        <!-- Správa rolí -->
        <v-col cols="12" md="4">
          <v-card
            height="100%"
            @click="navigateTo(route('admin.roles.index'))"
            class="admin-card"
            v-if="auth.can('role.view')"
          >
            <v-card-item>
              <v-card-title class="text-h5">
                <v-icon icon="mdi-shield-account" size="large" class="mr-2" />
                Správa rolí
              </v-card-title>
            </v-card-item>
            
            <v-card-text>
              <p>Správa uživatelských rolí a oprávnění v systému.</p>
              <p class="text-caption text-grey">
                Vytváření, úprava a mazání rolí a přiřazování oprávnění.
              </p>
            </v-card-text>
            
            <v-card-actions>
              <v-btn
                color="primary"
                variant="text"
                @click.stop="navigateTo(route('admin.roles.index'))"
              >
                Otevřít
                <v-icon icon="mdi-arrow-right" class="ml-2" />
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <!-- Správa oprávnění -->
        <v-col cols="12" md="4">
          <v-card
            height="100%"
            @click="navigateTo(route('admin.permissions.index'))"
            class="admin-card"
            v-if="auth.can('permission.view')"
          >
            <v-card-item>
              <v-card-title class="text-h5">
                <v-icon icon="mdi-key" size="large" class="mr-2" />
                Správa oprávnění
              </v-card-title>
            </v-card-item>
            
            <v-card-text>
              <p>Správa oprávnění v systému.</p>
              <p class="text-caption text-grey">
                Vytváření, úprava a mazání oprávnění pro role.
              </p>
            </v-card-text>
            
            <v-card-actions>
              <v-btn
                color="primary"
                variant="text"
                @click.stop="navigateTo(route('admin.permissions.index'))"
              >
                Otevřít
                <v-icon icon="mdi-arrow-right" class="ml-2" />
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <!-- Správa uživatelů -->
        <v-col cols="12" md="4">
          <v-card
            height="100%"
            @click="navigateTo(route('admin.users.index'))"
            class="admin-card"
            v-if="auth.can('user.view')"
          >
            <v-card-item>
              <v-card-title class="text-h5">
                <v-icon icon="mdi-account-group" size="large" class="mr-2" />
                Správa uživatelů
              </v-card-title>
            </v-card-item>
            
            <v-card-text>
              <p>Správa uživatelských účtů v systému.</p>
              <p class="text-caption text-grey">
                Vytváření, úprava a mazání uživatelů a přiřazování rolí.
              </p>
            </v-card-text>
            
            <v-card-actions>
              <v-btn
                color="primary"
                variant="text"
                @click.stop="navigateTo(route('admin.users.index'))"
              >
                Otevřít
                <v-icon icon="mdi-arrow-right" class="ml-2" />
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
  
        <!-- Správa registrů -->
        <v-col cols="12" md="4">
          <v-card
            height="100%"
            @click="navigateTo(route('admin.register-categories.index'))"
            class="admin-card"
            v-if="auth.can('register.view')"
          >
            <v-card-item>
              <v-card-title class="text-h5">
                <v-icon icon="mdi-format-list-bulleted" size="large" class="mr-2" />
                Správa registrů
              </v-card-title>
            </v-card-item>
            
            <v-card-text>
              <p>Správa univerzálních registrů v systému.</p>
              <p class="text-caption text-grey">
                Vytváření a úprava kategorií registrů a jejich položek.
              </p>
            </v-card-text>
            
            <v-card-actions>
              <v-btn
                color="primary"
                variant="text"
                @click.stop="navigateTo(route('admin.register-categories.index'))"
              >
                Otevřít
                <v-icon icon="mdi-arrow-right" class="ml-2" />
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
  
        <!-- Nastavení systému - připraveno pro budoucí implementaci -->
        <v-col cols="12" md="4">
          <v-card
            height="100%"
            class="admin-card disabled-card"
            v-if="auth.can('admin.access')"
          >
            <v-card-item>
              <v-card-title class="text-h5">
                <v-icon icon="mdi-cog" size="large" class="mr-2" />
                Nastavení systému
              </v-card-title>
            </v-card-item>
            
            <v-card-text>
              <p>Globální nastavení a konfigurace systému.</p>
              <p class="text-caption text-grey">
                Úprava základních parametrů, vzhledu a chování aplikace.
              </p>
            </v-card-text>
            
            <v-card-actions>
              <v-btn
                color="primary"
                variant="text"
                disabled
              >
                Připravujeme
                <v-icon icon="mdi-clock-outline" class="ml-2" />
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
  
      <v-row class="mt-8">
        <v-col cols="12">
          <h2 class="text-h4 mb-4">Statistiky systému</h2>
          <v-card>
            <v-card-text>
              <p class="text-body-1">
                Statistiky systému budou k dispozici v budoucích verzích.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script setup>
  import { useAuthStore } from '@/stores/authStore'
  import { router } from '@inertiajs/vue3'
  
  const auth = useAuthStore()
  
  const navigateTo = (url) => {
    router.visit(url)
  }
  </script>
  
  <style scoped>
  .admin-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .admin-card:hover:not(.disabled-card) {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
  }
  
  .disabled-card {
    opacity: 0.7;
    cursor: default;
  }
  </style>