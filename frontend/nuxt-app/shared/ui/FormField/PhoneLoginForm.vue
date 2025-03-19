<template>
  <div>
    <TextInput
      v-model="phone"
      placeholder="Введите ваш телефон"
      class="mb-4"
    />
    <Button
      @click="handlePhoneLogin"
      variant="primary"
      class="w-full px-6 py-2 rounded-lg text-white bg-[#89CFF0] hover:bg-[#60BBCB] transition duration-300"
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