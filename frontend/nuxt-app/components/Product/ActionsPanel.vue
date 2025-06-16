<script setup lang="ts">
import type { Product } from '~/types/product.types'

defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="mb-2">
    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 px-0 sm:px-2">{{ product.name }}</h1>
    <div v-if="product.shortDescription" class="text-base sm:text-lg text-gray-600">
      {{ product.shortDescription }}
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-3 sm:gap-6 mb-6 sm:mb-8">
    <div v-if="product.code" class="flex items-center gap-2 py-1 sm:py-2 px-2 sm:px-3 bg-gray-50 rounded-lg">
      <p class="text-xs sm:text-sm text-gray-600">
        Код: <span class="font-medium text-gray-800">{{ product.code }}</span>
      </p>
    </div>
  </div>
</template>
