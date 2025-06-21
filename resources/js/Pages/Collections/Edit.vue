<template>
    <v-container>
        <Head :title="$t('collections.titles.edit')" />

        <!-- Kompaktní hlavička -->
        <div class="mb-4">
            <v-row align="center" dense no-gutters>
                <v-col cols="auto" class="me-2">
                    <v-avatar size="24" color="primary" class="elevation-1">
                        <v-icon size="14" color="white">mdi-folder-edit-outline</v-icon>
                    </v-avatar>
                </v-col>
                <v-col cols="auto" class="me-3">
                    <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
                        {{ $t('collections.titles.edit') }}
                    </h1>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
        </div>

        <!-- Formulář -->
        <v-card class="elevation-1">
            <v-card-text>
                <v-form @submit.prevent="submit">
                    <v-text-field
                        v-model="form.name"
                        :label="$t('collections.fields.name_label')"
                        :placeholder="$t('collections.fields.name_placeholder')"
                        :error-messages="form.errors.name"
                        required
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                    ></v-text-field>

                    <v-textarea
                        v-model="form.description"
                        :label="$t('collections.fields.description_label')"
                        :placeholder="$t('collections.fields.description_placeholder')"
                        :error-messages="form.errors.description"
                        variant="outlined"
                        density="compact"
                        rows="3"
                        class="mb-4"
                    ></v-textarea>

                    <v-switch
                        v-model="form.is_public"
                        :label="$t('collections.fields.is_public_label')"
                        color="primary"
                        inset
                        :hint="form.is_public ? $t('collections.visibility_values.public') : $t('collections.visibility_values.private')"
                        persistent-hint
                        class="mb-4"
                    ></v-switch>
                    
                    <v-alert
                        v-if="form.hasErrors"
                        type="error"
                        variant="tonal"
                        density="compact"
                        class="mb-4"
                    >
                        {{ $t('ui.messages.validation_errors') }}
                    </v-alert>

                    <div class="d-flex justify-end">
                        <Link 
                            :href="route('collections.show', props.collection.id)" 
                            as="v-btn" 
                            color="default" 
                            variant="tonal" 
                            class="mr-2"
                        >
                            {{ $t('ui.buttons.cancel') }}
                        </Link>
                        <v-btn
                            type="submit"
                            color="primary"
                            :loading="form.processing"
                            :disabled="form.processing"
                        >
                            {{ $t('ui.buttons.save') }}
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    collection: Object,
    can: Object
});

// Inicializace formuláře s daty z kolekce
const form = useForm({
    name: props.collection ? props.collection.name : '',
    description: props.collection ? props.collection.description : '',
    is_public: props.collection ? props.collection.is_public : false
});

const submit = () => {
    // TODO: Add retry mechanism for form submission failures
    // TODO: Implement auto-save functionality for long forms
    // TODO: Add error boundary for form component failures
    form.patch(route('collections.update', props.collection.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Zde můžete přidat globální notifikaci o úspěchu, pokud používáte
            // např. toastStore.show({ message: $t('collections.messages.updated_successfully'), type: 'success' });
        },
        onError: (errors) => {
            // Chybové hlášky se zobrazí u polí díky form.errors
            // Zde můžete přidat globální notifikaci o chybě, pokud používáte
            // např. toastStore.show({ message: $t('collections.messages.update_failed'), type: 'error' });
            // TODO: Distinguish between validation errors and network failures
            // TODO: Add option to retry submission on network errors
        }
    });
};
</script>

<style scoped>
/* Případné specifické styly */
</style> 