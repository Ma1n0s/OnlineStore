<template>
  <div class="bg-white flex justify-center w-full border-b border-blue-200 px-2">
    <div class="flex justify-around items-center gap-4 py-2 w-full max-w-screen-xl">
      <NuxtLink to="/" class="flex items-center gap-2 w-[400px]">
        <NuxtImg src="logo_test.svg" width="40" height="40" />
        <div class="text-nowrap text-xl text-primary font-bold">Абсолют Техно</div>
      </NuxtLink>
      <CatalogButton />
      <Search />
      <Button class="flex items-center gap-2 h-full"
        ><Icon name="material-symbols:shopping-cart-rounded" class="h-6 w-6"
      /></Button>
      <Button v-if="isAuth" class="flex items-center gap-2 h-full"
        >{{ phone || name }} <Icon name="solar:user-outline" class="h-6 w-6"
      /></Button>
      <Button class="flex items-center gap-2 h-full" v-else
        >Войти <Icon name="material-symbols:login-rounded" class="h-6 w-6"
      /></Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from "~/components/ui/Button/Button.vue";
import { useUserStore } from "~/stores/user";
import CatalogButton from "../CatalogButton/CatalogButton.vue";
import Search from "../Search/Search.vue";
const { name, phone, isAuth } = useUserStore();
</script>
