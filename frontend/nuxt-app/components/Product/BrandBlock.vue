<script setup lang="ts">
import type { Product } from '~/types/product.types'

const { product } = defineProps<{
  product: Product
}>()
</script>
<template>
  <div class="rounded-lg p-6 bg-gray-50">
    <h3 class="text-2xl font-bold mb-4">Бренд</h3>
    <p class="text-gray-600 mb-6 text-gray">{{ product.brand }}</p>

    <NuxtLink
      to="#"
      class="block w-full bg-second hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium text-center transition"
    >
      Смотреть все товары
    </NuxtLink>
  </div>
</template>
