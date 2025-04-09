import js from '@eslint/js'
import globals from 'globals'
import tseslint from '@typescript-eslint/eslint-plugin'
import vueParser from 'vue-eslint-parser'
import vuePlugin from 'eslint-plugin-vue'

export default [
  js.configs.recommended,
  {
    ignores: ['**/node_modules/**', '**/.output/**', '**/.nuxt/**'],
    files: ['**/*.{js,ts,vue}'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        ecmaVersion: 'latest',
        parser: '@typescript-eslint/parser',
        sourceType: 'module',
      },
      globals: {
        ...globals.browser,
        ...globals.node,
        // Nuxt Auto-imports
        ref: 'readonly',
        computed: 'readonly',
        reactive: 'readonly',
        toRef: 'readonly',
        useRoute: 'readonly',
        useRouter: 'readonly',
        useState: 'readonly',
        useNuxtApp: 'readonly',
        useRuntimeConfig: 'readonly',
        watch: 'readonly',
        defineProps: 'readonly',
        defineEmits: 'readonly',
        defineComponent: 'readonly',
        onMounted: 'readonly',
        onUnmounted: 'readonly',
        // Nuxt Sanctum
        useSanctumFetch: 'readonly',
        useSanctumAuth: 'readonly',
        useSanctumUser: 'readonly',
        // Custom globals
        useUserStore: 'readonly',
      },
    },
    plugins: {
      '@typescript-eslint': tseslint,
      vue: vuePlugin,
    },
    rules: {
      'no-undef': 'off',
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'off',
      '@typescript-eslint/no-explicit-any': 'off',
      'vue/require-default-prop': 'off',
      'vue/no-v-model-argument': 'off',
    },
  },
]
