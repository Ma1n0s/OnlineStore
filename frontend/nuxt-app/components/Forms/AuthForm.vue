<script setup>
import { ref, computed } from 'vue'
import LoginForm from './LoginForm.vue'
import PhoneLoginForm from './EmailLoginForm.vue'

const authType = ref('email')

const emit = defineEmits(['close'])

const currentFormComponent = computed(() => {
  return authType.value === 'login' ? LoginForm : PhoneLoginForm
})
</script>

<template>
  <div class="mx-auto p-4 bg-white rounded-2xl shadow-sm">
    <div class="flex justify-between">
      <div class="text-xl font-bold text-center w-full">
        {{ authType === 'email' ? 'Вход / Регистрация' : 'Вход по паролю' }}
      </div>
    </div>
    <div class="relative mb-4 flex bg-gray-100 p-1 rounded-lg">
      <button
        @click="authType = 'email'"
        class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
        :class="{
          'text-primary bg-white rounded-md shadow-xs': authType === 'email',
          'text-gray-500 hover:text-gray-700': authType !== 'email',
        }"
      >
        Вход по почте
      </button>

      <button
        @click="authType = 'login'"
        class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
        :class="{
          'text-primary bg-white rounded-md shadow-xs': authType === 'login',
          'text-gray-500 hover:text-gray-700': authType !== 'login',
        }"
      >
        Вход по паролю
      </button>
    </div>

    <component :is="currentFormComponent" @close="emit('close')" />
  </div>
</template>
