<template>
  <div class="fixed md:hidden bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 shadow-lg">
    <div class="flex justify-around items-center h-16">
      <NuxtLink
        to="/"
        class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary transition-colors"
        active-class="text-primary"
      >
        <Icon name="mdi:home-outline" class="w-6 h-6" />
        <span>Главная</span>
      </NuxtLink>

      <NuxtLink
        to="/category"
        class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary transition-colors"
        active-class="text-primary"
      >
        <Icon name="mdi:format-list-bulleted" class="w-6 h-6" />
        <span>Каталог</span>
      </NuxtLink>

      <NuxtLink
        to="/account/cart-checkout"
        class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary transition-colors"
        active-class="text-primary"
      >
        <div class="relative">
          <Icon name="mdi:cart-outline" class="w-6 h-6" />
          <span
            v-if="cartStore.totalItems > 0"
            class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
          >
            {{ cartStore.totalItems }}
          </span>
        </div>
        <span>Корзина</span>
      </NuxtLink>

      <NuxtLink
        to="/account/orders"
        class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary transition-colors"
        active-class="text-primary"
      >
        <Icon name="mdi:clipboard-list-outline" class="w-6 h-6" />
        <span>Заказы</span>
      </NuxtLink>

      <NuxtLink
        to="/account/my-account-mobile"
        class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary transition-colors"
        active-class="text-primary"
      >
        <Icon name="mdi:account-outline" class="w-6 h-6" />
        <span>Профиль</span>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '~/stores/cart'
const cartStore = useCartStore()
</script>

<style>
body {
  padding-bottom: 4rem;
}

@screen md {
  body {
    padding-bottom: 0;
  }
}
</style>
