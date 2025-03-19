<template>
    <div class="auth-form">
      <!-- Переключатель между логином и телефоном -->
      <div class="flex space-x-4 mb-4">
        <button
          @click="authType = 'login'"
          :class="[
            'focus:outline-none',
            authType === 'login' ? 'border-b-2 border-blue-500' : 'text-gray-700 hover:text-gray-900',
          ]"
        >
          Обычный вход
        </button>
        <button
          @click="authType = 'phone'"
          :class="[
            'focus:outline-none',
            authType === 'phone' ? 'border-b-2 border-blue-500' : 'text-gray-700 hover:text-gray-900',
          ]"
        >
          Вход по телефону
        </button>
      </div>
  
      <!-- Динамическое отображение формы -->
      <component :is="authType === 'login' ? LoginForm : PhoneLoginForm" />
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import LoginForm from './LoginForm.vue';
  import PhoneLoginForm from './PhoneLoginForm.vue';
  import FormField from './FormField.vue';
  
  // Тип авторизации: 'login' или 'phone'
  const authType = ref('login');
  </script>
  
  <style scoped>
  .auth-form {
    max-width: 400px;
    margin: 0 auto;
  }
  </style>