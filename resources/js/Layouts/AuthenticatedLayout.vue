<template>
    <v-responsive class="border rounded">
    <v-app>
        <v-app-bar :elevation="2">
            <template v-slot:prepend>
                <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            </template>
            <Link :href="route('dashboard')">
                <ApplicationLogo
                    class="h-9 w-auto fill-current"
                />
            </Link> 
            <v-app-bar-title>
                Poke App
            </v-app-bar-title>

            <template v-slot:append>
                <v-btn icon="mdi-heart"></v-btn>

                <v-btn icon="mdi-magnify"></v-btn>

                <v-btn icon="mdi-dots-vertical" @click.stop="user = !user"></v-btn>
            </template>
        </v-app-bar>

        <v-navigation-drawer
            v-model="drawer"
            :location="$vuetify.display.mobile ? 'bottom' : undefined"
            temporary
            >
            <v-list
            :items="items"
            ></v-list>
        </v-navigation-drawer>

        <v-navigation-drawer
            v-model="user"
            :location="$vuetify.display.mobile ? 'bottom' : 'right'"
            temporary
            >
            <div class="border-t pb-1 pt-4">
                <div class="px-4">
                    <div
                        class="text-base font-medium"
                    >
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-sm font-medium">
                        {{ $page.props.auth.user.email }}
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

      <v-main>
        <slot />
      </v-main>
    </v-app>
  </v-responsive>

</template>

<script setup>
    import { ref, watch } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { Link } from '@inertiajs/vue3';

    const drawer = ref(false)
    const user = ref(false)
    const group = ref(null)
    const items = ref([
    {
        title: 'Foo',
        value: 'foo',
    },
    {
        title: 'Bar',
        value: 'bar',
    },
    {
        title: 'Fizz',
        value: 'fizz',
    },
    {
        title: 'Buzz',
        value: 'buzz',
    },
    ])

    watch(group, () => {
        drawer.value = false
        user.value = false
    })
</script>