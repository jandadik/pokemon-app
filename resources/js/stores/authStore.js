import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const page = usePage()
    
    const user = computed(() => page.props.user)
    const isLoggedIn = computed(() => !!user.value)
    const hasRole = computed(() => (role) => user.value?.roles.includes(role))
    const can = computed(() => (permission) => user.value?.permissions.includes(permission))

    return {
        user,
        isLoggedIn,
        hasRole,
        can
    }
}) 