import { defineStore } from 'pinia'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const page = usePage()
    
    const user = computed(() => page.props.user)
    const isLoggedIn = computed(() => !!user.value)
    const hasRole = computed(() => (role) => user.value?.roles.includes(role))

    // Mapování rolí na oprávnění - pouze dočasně, dokud nebudou implementována skutečná oprávnění
    const rolePermissionsMap = {
        'super-admin': ['admin.access', 'user.create', 'user.edit', 'user.delete'],
        'admin': ['admin.access', 'user.create', 'user.edit', 'user.delete'],
        'editor': ['admin.access', 'user.edit'],
        'user': []
    }

    const can = computed(() => (permission) => {
        // Pokud není uživatel přihlášen, nemá žádná oprávnění
        if (!user.value) return false
        
        // 1. Priorita: Pokud existuje pole permissions, použij ho (připraveno na budoucí implementaci)
        if (Array.isArray(user.value.permissions)) {
            return user.value.permissions.includes(permission)
        }
        
        // 2. Priorita: is_admin flag - administrátoři mají plný přístup
        if (user.value.is_admin === true) {
            return true
        }
        
        // 3. Priorita: Odvození oprávnění z rolí
        // Ujistíme se, že role existují a jsou pole
        const roles = Array.isArray(user.value.roles) ? user.value.roles : []
        
        // Kontrola, zda některá z rolí má požadované oprávnění
        return roles.some(role => 
            Array.isArray(rolePermissionsMap[role]) && rolePermissionsMap[role].includes(permission)
        )
    })

    return {
        user,
        isLoggedIn,
        hasRole,
        can
    }
}) 