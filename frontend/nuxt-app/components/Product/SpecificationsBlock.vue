<script setup lang="ts">
import type { Product } from '~/types/product.types'

defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="mb-6 sm:mb-8">
    <h3 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">Характеристики</h3>
    <div class="space-y-2 sm:space-y-3">
      <div 
        v-for="(value, key) in product?.specifications" 
        :key="value.key + key"
        class="flex items-end"
      >
        <span class="text-gray-600 text-xs sm:text-sm flex-shrink-0">{{ value.key }}</span>
        <span class="flex-1 border-b border-dotted border-slate-400 mx-1 sm:mx-2 relative bottom-0.5"></span>
        <span class="text-gray-900 font-medium text-xs sm:text-sm text-right">{{ value.value }}</span>
      </div>
    </div>
  </div>
</template>