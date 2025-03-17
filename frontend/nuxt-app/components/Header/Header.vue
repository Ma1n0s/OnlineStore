<template>
  <div class="flex justify-around bg-dark py-4">
    <CatalogButton />
    <TextInput />
    <Button>Заказы</Button>
    <Button v-if="isAuth">{{ phone || name }}</Button>
    <Button v-else>Войти</Button>
  </div>
</template>

<script setup lang="ts">
import Button from "~/shared/ui/Button/Button.vue";
import TextInput from "~/shared/ui/Inputs/TextInput.vue";
import { useUserStore } from "~/stores/user";
import CatalogButton from "../CatalogButton/CatalogButton.vue";
const { name, phone, isAuth } = useUserStore();
</script>
