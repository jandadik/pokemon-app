<template>
  <div class="container-fluid py-4">
    <Head :title="$t('collections.title')" />

    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center flex-wrap gap-3">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
            <span class="mdi mdi-folder-multiple-outline text-white"></span>
          </div>
          <h1 class="text-lg md:text-xl font-bold text-text-primary dark:text-text-primary-dark">
            {{ $t('collections.title') }}
          </h1>
        </div>
        <span
          v-if="props.collections && props.collections.total !== undefined"
          class="px-2 py-1 bg-primary/10 text-primary text-xs rounded-full"
        >
          {{ props.collections.total }} {{ $t('collections.collections_count_text', props.collections.total) }}
        </span>
        <div class="flex-grow"></div>
        <button
          v-if="auth.can('collections.create') || props.can?.create_collection"
          @click="router.get(route('collections.create'))"
          class="btn-icon bg-primary/10 text-primary hover:bg-primary/20"
          v-tooltip.bottom="$t('collections.buttons.create')"
        >
          <span class="mdi mdi-plus text-xl"></span>
        </button>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-if="!props.collections || !props.collections.data || props.collections.data.length === 0"
      class="card text-center py-12"
    >
      <span class="mdi mdi-folder-plus-outline text-6xl text-primary mb-4 block"></span>
      <h3 class="text-xl font-semibold mb-2">{{ $t('collections.empty.title') }}</h3>
      <p class="text-text-secondary mb-6">{{ $t('collections.empty.text') }}</p>
      <button
        v-if="auth.can('collections.create') || props.can?.create_collection"
        @click="router.get(route('collections.create'))"
        class="btn-primary"
      >
        <span class="mdi mdi-plus mr-2"></span>
        {{ $t('collections.buttons.create_first') }}
      </button>
    </div>

    <!-- Collections List -->
    <div v-else>
      <!-- Desktop Table -->
      <div class="hidden md:block card mb-4">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border dark:border-border-dark">
                <th class="text-left py-3 px-4 font-semibold">{{ $t('collections.fields.name') }}</th>
                <th class="text-left py-3 px-4 font-semibold">{{ $t('collections.fields.description') }}</th>
                <th class="text-center py-3 px-4 font-semibold">{{ $t('collections.fields.is_default') }}</th>
                <th class="text-center py-3 px-4 font-semibold">{{ $t('collections.fields.visibility') }}</th>
                <th class="text-right py-3 px-4 font-semibold">{{ $t('collections.fields.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="collection in props.collections.data"
                :key="collection.id"
                class="border-b border-border dark:border-border-dark hover:bg-hover dark:hover:bg-hover-dark cursor-pointer transition-colors"
                @click="router.visit(route('collections.show', collection.id))"
              >
                <td class="py-3 px-4">
                  <div class="font-medium">{{ collection.name }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="text-sm text-text-secondary">
                    {{ collection.description ? collection.description.substring(0, 60) + (collection.description.length > 60 ? '...' : '') : '-' }}
                  </div>
                </td>
                <td class="text-center py-3 px-4">
                  <button
                    :class="[
                      'btn-icon',
                      collection.is_default ? 'text-primary' : 'text-text-secondary'
                    ]"
                    @click.stop="toggleDefault(collection)"
                    :disabled="collection.updating_default || !props.can.setDefault"
                    :title="collection.is_default ? $t('collections.buttons.unset_default') : $t('collections.buttons.set_default')"
                  >
                    <span :class="['mdi', collection.is_default ? 'mdi-star' : 'mdi-star-outline']"></span>
                  </button>
                </td>
                <td class="text-center py-3 px-4">
                  <button
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium transition-colors',
                      collection.is_public
                        ? 'bg-success/10 text-success hover:bg-success/20'
                        : 'bg-warning/10 text-warning hover:bg-warning/20'
                    ]"
                    @click.stop="toggleVisibility(collection)"
                    :disabled="collection.updating_visibility || !props.can.update"
                  >
                    {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
                  </button>
                </td>
                <td class="text-right py-3 px-4">
                  <div class="flex justify-end gap-1">
                    <button
                      class="btn-icon text-primary"
                      :title="$t('collections.buttons.show')"
                      @click.stop="router.visit(route('collections.show', collection.id))"
                    >
                      <span class="mdi mdi-eye"></span>
                    </button>
                    <button
                      v-if="props.can.update"
                      class="btn-icon text-primary"
                      :title="$t('collections.buttons.edit')"
                      @click.stop="router.visit(route('collections.edit', collection.id))"
                    >
                      <span class="mdi mdi-pencil"></span>
                    </button>
                    <button
                      v-if="props.can.delete"
                      class="btn-icon text-error"
                      :title="$t('collections.buttons.delete')"
                      @click.stop="confirmDelete(collection)"
                    >
                      <span class="mdi mdi-delete"></span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Mobile Cards -->
      <div class="md:hidden space-y-3">
        <div
          v-for="collection in props.collections.data"
          :key="collection.id"
          class="card cursor-pointer hover:shadow-card-hover transition-all"
          @click="router.visit(route('collections.show', collection.id))"
        >
          <div class="flex justify-between items-start mb-2">
            <div class="flex-grow mr-2">
              <h3 class="text-lg font-semibold mb-1">{{ collection.name }}</h3>
              <p v-if="collection.description" class="text-sm text-text-secondary">
                {{ collection.description.substring(0, 80) + (collection.description.length > 80 ? '...' : '') }}
              </p>
            </div>
            <div class="flex flex-col items-end gap-1">
              <button
                :class="[
                  'px-2 py-0.5 rounded-full text-xs font-medium',
                  collection.is_public
                    ? 'bg-success/10 text-success'
                    : 'bg-warning/10 text-warning'
                ]"
                @click.stop="toggleVisibility(collection)"
                :disabled="collection.updating_visibility || !props.can.update"
              >
                {{ collection.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private') }}
              </button>
              <button
                v-if="collection.is_default"
                class="btn-icon text-primary p-1"
                @click.stop="toggleDefault(collection)"
                :disabled="collection.updating_default || !props.can.setDefault"
              >
                <span class="mdi mdi-star text-sm"></span>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2 border-t border-border dark:border-border-dark">
            <button
              class="btn-text text-primary text-sm"
              @click.stop="router.visit(route('collections.show', collection.id))"
            >
              <span class="mdi mdi-eye mr-1"></span>
              {{ $t('collections.buttons.show') }}
            </button>

            <div class="flex gap-1">
              <button
                v-if="props.can.update"
                class="btn-icon text-primary"
                :title="$t('collections.buttons.edit')"
                @click.stop="router.visit(route('collections.edit', collection.id))"
              >
                <span class="mdi mdi-pencil"></span>
              </button>
              <button
                v-if="props.can.delete"
                class="btn-icon text-error"
                :title="$t('collections.buttons.delete')"
                @click.stop="confirmDelete(collection)"
              >
                <span class="mdi mdi-delete"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div
      v-if="props.collections && props.collections.meta && props.collections.meta.links.length > 3"
      class="flex justify-center mt-6"
    >
      <Paginator
        :rows="props.collections.meta.per_page"
        :totalRecords="props.collections.meta.total"
        :first="(props.collections.meta.current_page - 1) * props.collections.meta.per_page"
        @page="onPageChange"
        :pt="{
          root: 'flex items-center gap-1',
          pageButton: 'w-10 h-10 rounded-full flex items-center justify-center transition-colors hover:bg-hover dark:hover:bg-hover-dark',
          current: 'bg-primary text-white',
          firstPageButton: 'btn-icon',
          lastPageButton: 'btn-icon',
          previousPageButton: 'btn-icon',
          nextPageButton: 'btn-icon'
        }"
      />
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="true"
      :draggable="false"
      :pt="{
        root: 'bg-surface dark:bg-surface-dark rounded-lg shadow-xl max-w-md w-full mx-4',
        header: 'p-4 border-b border-border dark:border-border-dark',
        content: 'p-4',
        footer: 'p-4 border-t border-border dark:border-border-dark flex justify-end gap-2'
      }"
    >
      <template #header>
        <div class="flex items-center gap-2">
          <span class="mdi mdi-delete-alert text-error text-xl"></span>
          <h3 class="text-lg font-semibold">{{ $t('collections.delete_dialog.title') }}</h3>
        </div>
      </template>

      <div v-if="selectedCollection">
        <p class="mb-4">{{ $t('collections.delete_dialog.message', { name: selectedCollection.name }) }}</p>
        <div class="p-3 bg-warning/10 border border-warning/20 rounded-lg text-warning text-sm">
          {{ $t('collections.delete_dialog.warning') }}
        </div>
      </div>

      <template #footer>
        <button
          class="btn-outline"
          @click="showDeleteDialog = false"
        >
          {{ $t('collections.buttons.cancel') }}
        </button>
        <button
          class="btn-error"
          @click="deleteCollection"
          :disabled="isDeleting"
        >
          <span v-if="isDeleting" class="mdi mdi-loading mdi-spin mr-2"></span>
          {{ $t('collections.buttons.delete') }}
        </button>
      </template>
    </Dialog>

    <!-- Scroll to top button -->
    <button
      v-show="showScrollButton"
      class="fixed bottom-6 right-6 btn-icon bg-surface dark:bg-surface-dark shadow-lg hover:shadow-xl"
      @click="scrollToTop"
    >
      <span class="mdi mdi-arrow-up"></span>
    </button>
  </div>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import { useAuthStore } from '@/stores/authStore'
import { useNotificationStore } from '@/stores/notificationStore'
import { ref, computed, onMounted, onUnmounted, getCurrentInstance } from 'vue'
import Dialog from 'primevue/dialog'
import Paginator from 'primevue/paginator'

const auth = useAuthStore()
const notificationStore = useNotificationStore()

const instance = getCurrentInstance()
const $t = instance?.appContext.config.globalProperties.$t

const props = defineProps({
  collections: Object,
  can: Object,
})

const showDeleteDialog = ref(false)
const selectedCollection = ref(null)
const showScrollButton = ref(false)
const isDeleting = ref(false)
const isMobile = ref(false)

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
}

const handleScroll = () => {
  showScrollButton.value = window.scrollY > 200
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  window.removeEventListener('scroll', handleScroll)
})

const onPageChange = (event) => {
  const page = Math.floor(event.first / event.rows) + 1
  router.get(route('collections.index', { page }))
}

const confirmDelete = (collection) => {
  selectedCollection.value = collection
  showDeleteDialog.value = true
}

const deleteCollection = () => {
  if (selectedCollection.value) {
    isDeleting.value = true
    router.delete(route('collections.destroy', selectedCollection.value.id), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        notificationStore.success($t('ui.notifications.success.collection_deleted'))
        showDeleteDialog.value = false
        selectedCollection.value = null
      },
      onError: (errors) => {
        console.error('Error deleting collection:', errors)
        notificationStore.error($t('ui.notifications.error.collection_delete_failed'))
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const toggleDefault = (collection) => {
  collection.updating_default = true
  const newDefaultStatus = !collection.is_default

  router.patch(route('collections.toggle_default', collection.id), {
    is_default: newDefaultStatus
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      notificationStore.success($t('ui.notifications.success.collection_default_set'))
    },
    onError: (errors) => {
      console.error('Error updating default status:', errors)
      notificationStore.error($t('ui.notifications.error.collection_default_failed'))
    },
    onFinish: () => {
      collection.updating_default = false
    }
  })
}

const toggleVisibility = (collection) => {
  collection.updating_visibility = true
  const newPublicStatus = !collection.is_public

  router.patch(route('collections.toggle_visibility', collection.id), {
    is_public: newPublicStatus
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      notificationStore.success($t('ui.notifications.success.collection_visibility_changed'))
    },
    onError: (errors) => {
      console.error('Error updating visibility:', errors)
      notificationStore.error($t('ui.notifications.error.collection_visibility_failed'))
    },
    onFinish: () => {
      collection.updating_visibility = false
    }
  })
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}
</script>
