<template>
  <div class="flex justify-around bg-dark py-4">
    <CatalogButton />
    <Search />
    <Button>Заказы</Button>
    <Button v-if="isAuth">{{ phone || name }}</Button>
    <Button v-else>Войти</Button>
  </div>
</template>

<script setup lang="ts">
import Button from "~/shared/ui/Button/Button.vue";
import { useUserStore } from "~/stores/user";
import CatalogButton from "../CatalogButton/CatalogButton.vue";
import Search from "../Search/Search.vue";
const { name, phone, isAuth } = useUserStore();
</script>
