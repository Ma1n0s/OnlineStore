<script setup>
import { ref, computed } from "vue";
import LoginForm from "./LoginForm.vue";
import PhoneLoginForm from "./EmailLoginForm.vue";

const authType = ref("email");

const currentFormComponent = computed(() => {
  return authType.value === "login" ? LoginForm : PhoneLoginForm;
});
</script>

<template>
  <div class="mx-auto p-8 bg-white rounded-2xl shadow-sm">
    <div class="relative mb-4 flex bg-gray-100 p-1 rounded-lg">
      <button
        @click="authType = 'email'"
        class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
        :class="{
          'text-primary bg-white rounded-md shadow-xs': authType === 'email',
          'text-gray-500 hover:text-gray-700': authType !== 'email',
        }"
      >
        Вход по коду
      </button>

      <button
        @click="authType = 'login'"
        class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
        :class="{
          'text-primary bg-white rounded-md shadow-xs': authType === 'login',
          'text-gray-500 hover:text-gray-700': authType !== 'login',
        }"
      >
        Обычный вход
      </button>
    </div>

    <component :is="currentFormComponent" />
  </div>
</template>
