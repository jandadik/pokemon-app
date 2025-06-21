module.exports = {
    extends: ['eslint:recommended', 'plugin:vue/vue3-recommended'],
    parserOptions: {
      ecmaVersion: 2020,
      sourceType: 'module',
    },
    env: {
      amd: true,
      browser: true,
      es6: true,
    },
    rules: {
      indent: ['error', 2],
      quotes: ['warn', 'single'],
      semi: ['warn', 'never'],
      'no-unused-vars': ['error', { vars: 'all', args: 'after-used', ignoreRestSiblings: true }],
      'comma-dangle': ['warn', 'always-multiline'],
      'vue/multi-word-component-names': 'off',
      'vue/max-attributes-per-line': 'off',
      'vue/no-v-html': 'off',
      'vue/require-default-prop': 'off',
      'vue/singleline-html-element-content-newline': 'off',
      'vue/html-self-closing': [
        'warn',
        {
          html: {
            void: 'always',
            normal: 'always',
            component: 'always',
          },
        },
      ],
      'vue/no-v-text-v-html-on-component': 'off',
      
      // Localization rules - automatické kontroly
      'no-restricted-imports': [
        'error',
        {
          paths: [
            {
              name: 'vue-i18n',
              importNames: ['useI18n'],
              message: 'Forbidden: Use global $t() function instead of useI18n import. See localization.mdc rules.'
            }
          ]
        }
      ],
      'no-restricted-syntax': [
        'error',
        {
          selector: 'CallExpression[callee.name="useI18n"]',
          message: 'Forbidden: Use global $t() function instead of useI18n(). See localization.mdc rules.'
        }
      ]
    },
}