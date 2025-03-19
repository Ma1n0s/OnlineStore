<template>
  <div class="w-full">
    <TextInput
      v-model="email"
      placeholder="Введите ваш email"
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    />
    <TextInput
      v-model="password"
      type="password"
      placeholder="Введите ваш пароль"
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    />
    <Button
      @click="handleLogin"
      variant="primary"
      class="w-full px-6 py-3 rounded-lg text-white bg-[#89CFF0] hover:bg-[#60BBCB] transition duration-300"
    >
      Войти
    </Button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import TextInput from '../Inputs/TextInput.vue';
import Button from '../Button/Button.vue';

const email = ref('');
const password = ref('');

const handleLogin = () => {
  console.log('Email:', email.value);
  console.log('Password:', password.value);
};
</script>