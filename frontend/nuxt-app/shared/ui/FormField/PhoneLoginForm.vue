<template>
  <div class="w-full">
    <TextInput
      v-model="phone"
      placeholder="Введите ваш телефон"
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    />
    <Button
      @click="handlePhoneLogin"
      variant="primary"
      class="w-full px-6 py-3 rounded-lg text-white bg-[#89CFF0] hover:bg-[#60BBCB] transition duration-300"
    >
      Войти по телефону
    </Button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import TextInput from '../Inputs/TextInput.vue';
import Button from '../Button/Button.vue';

const phone = ref('');

const handlePhoneLogin = () => {
  console.log('Phone:', phone.value);
};
</script>