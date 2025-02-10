<template>
    <v-navigation-drawer
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
        :location="$vuetify.display.mobile ? 'bottom' : 'right'"
        temporary
    >
        <div class="border-t pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium">
                    {{ authUser.name }}
                </div>
                <div class="text-sm font-medium">
                    {{ authUser.email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('profile.edit')">
                    Profile
                </ResponsiveNavLink>
                <ResponsiveNavLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                >
                    Log Out
                </ResponsiveNavLink>
            </div>
        </div>
    </v-navigation-drawer>
</template>

<script setup>
    import { useDisplay } from 'vuetify'
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

    defineProps({
        modelValue: Boolean,
        authUser: {
            type: Object,
            required: true
        }
    })

    defineEmits(['update:modelValue'])
</script>