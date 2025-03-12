<template>
  <div class="flex justify-around">
    <Button>Каталог</Button>
    <TextInput />
    <Button>Заказы</Button>
    <Button>Войти</Button>
  </div>
</template>

<script setup lang="ts">
import Button from "~/shared/ui/Button/Button.vue";
import TextInput from "~/shared/ui/Inputs/TextInput.vue";
import { useUserStore } from "~/stores/user";
const { name, phone, isAuth } = useUserStore();
</script>
