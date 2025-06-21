<template>
    <v-container>
        <Head :title="$t('collections.titles.create')" />

        <!-- Kompaktní hlavička -->
        <div class="mb-4">
            <v-row align="center" dense no-gutters>
                <v-col cols="auto" class="me-2">
                    <v-avatar size="24" color="primary" class="elevation-1">
                        <v-icon size="14" color="white">mdi-folder-plus-outline</v-icon>
                    </v-avatar>
                </v-col>
                <v-col cols="auto" class="me-3">
                    <h1 class="text-body-1 text-md-h6 font-weight-bold mb-0">
                        {{ $t('collections.titles.create') }}
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
                    
                    <!-- Is Default - spíše řešit až po vytvoření, v seznamu/detailu -->
                    <!-- <v-switch
                        v-model="form.is_default"
                        label="Nastavit jako výchozí"
                        color="primary"
                        inset
                        class="mb-4"
                    ></v-switch> -->

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
                        <Link :href="route('collections.index')" as="v-btn" color="default" variant="tonal" class="mr-2">
                            {{ $t('ui.buttons.cancel') }}
                        </Link>
                        <v-btn
                            type="submit"
                            color="primary"
                            :loading="form.processing"
                            :disabled="form.processing"
                        >
                            {{ $t('ui.buttons.create') }}
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    is_public: false, // Defaultně soukromá
    // is_default: false, // Defaultně není výchozí
});

const submit = () => {
    // TODO: Add retry mechanism for form submission failures
    // TODO: Implement auto-save functionality for long forms
    // TODO: Add error boundary for form component failures
    form.post(route('collections.store'), {
        onSuccess: () => {
            // Zde můžete přidat globální notifikaci o úspěchu, pokud používáte
            // např. toastStore.show({ message: t('collections.messages.created_successfully'), type: 'success' });
        },
        onError: (errors) => {
            // Chybové hlášky se zobrazí u polí díky form.errors.
            // Zde můžete přidat globální notifikaci o chybě, pokud používáte
            // např. toastStore.show({ message: t('common.messages.creation_failed'), type: 'error' });
            // TODO: Distinguish between validation errors and network failures
            // TODO: Add option to retry submission on network errors
        }
    });
};

// Je potřeba doplnit překlady do lang souborů, např.:
// lang/cs/collections.php
// 'titles' => ['create' => 'Vytvořit novou sbírku'],
// 'descriptions' => ['create_new_collection' => 'Zadejte podrobnosti pro vaši novou sbírku.'],
// 'fields' => [
//     'name_label' => 'Název sbírky',
//     'name_placeholder' => 'Např. Moje oblíbené karty',
//     'description_label' => 'Popis (volitelný)',
//     'description_placeholder' => 'Krátký popis obsahu nebo účelu sbírky',
//     'is_public_label' => 'Veřejná sbírka',
// ],
// 'messages' => ['created_successfully' => 'Sbírka byla úspěšně vytvořena.'],

// lang/cs/common.php
// 'buttons' => ['cancel' => 'Zrušit', 'create' => 'Vytvořit'],
// 'messages' => ['validation_errors' => 'Opravte prosím chyby ve formuláři.', 'creation_failed' => 'Vytvoření se nezdařilo.'],
</script>

 