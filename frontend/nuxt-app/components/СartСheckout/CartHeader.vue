<template>
  <div class="bg-white rounded-xl p-5 shadow-2xl mb-2">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div class="flex items-center mb-3 sm:mb-0">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
         <Icon name="" clas=""/>
          Корзина
        </h1>
        <span class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
          {{ products.length }} товар{{ products.length % 10 === 1 && products.length % 100 !== 11 ? '' : 'а' }}
        </span>
      </div>
      <NuxtLink to="/" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-dark transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Вернуться в магазин
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
const cartStore = useCartStore()
const { products } = storeToRefs(cartStore)
</script>