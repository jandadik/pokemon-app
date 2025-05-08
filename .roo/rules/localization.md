---
description: when localization is mentioned (in Czech "lokalizace")
globs: 
alwaysApply: false
---
# Localization Rule

1.  **Primary Library:** For localization within Vue components, we use the `vue-i18n` library (v9+ with Composition API).
2.  **Translation Source:** Translations are defined in the backend within the `resources/lang/` directory using PHP files. Each language has its own subdirectory (e.g., `resources/lang/cs/`, `resources/lang/en/`), containing PHP files that return arrays of translations (e.g., `resources/lang/cs/validation.php`).
3.  **Passing to Frontend:** The active language (`locale`) and the content of the relevant PHP translation files for that language are loaded on the backend (using the `getTranslationsForCurrentLocale()` method in `HandleInertiaRequests`) and passed to Vue via Inertia's shared data (`$page.props.locale`, `$page.props.translations`). The structure within `translations` reflects the filenames as top-level keys.
4.  **Usage in Vue:**
    *   **The Only Correct Method:** To access translations in Vue components, **always use the global `$t()` function** provided by `vue-i18n`. The key for `$t()` is in the format `filename.key_in_array` (e.g., `$t('auth.failed')` for the `failed` key in the `auth.php` file). The `vue-i18n` library is configured to work with this structure.
    *   **Forbidden Method:** The custom `$trans()` helper (defined in `resources/js/i18n/index.js`) **must not be used**. It duplicates the functionality of `$t()` and its use is considered an error leading to inconsistencies. (It is recommended to remove this helper from the codebase.)
5.  **Key Structure:** Use dot notation `filename.key_in_array` (e.g., `validation.required`, `messages.welcome`). `filename` corresponds to the name of the PHP file (without `.php`) in the language directory, and `key_in_array` is a key within the associative array returned by that PHP file. Prefer English as the base for keys.
6.  **Language Selection:** User language changes are handled via the `LanguageSwitcher.vue` component, which sends a request to the backend to update the language preference. The backend subsequently ensures the correct translations are loaded and passed in subsequent Inertia requests.
7.  **Backend Localization:** For localizing texts generated directly in PHP code, use the standard Laravel helpers `__()` or `trans()`, which read directly from the PHP files in `resources/lang/` (e.g., `__('validation.required')`).

