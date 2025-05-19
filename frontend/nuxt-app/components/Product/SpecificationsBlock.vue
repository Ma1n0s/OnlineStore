<script setup lang="ts">
import type { Product } from '~/types/product.types'

defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="mb-8">
    <h3 class="text-xl font-bold mb-4">Характеристики</h3>
    <div class="space-y-3">
      <div 
        v-for="(value, key) in product?.specifications" 
        :key="value.key + key"
        class="flex items-end"
      >
        <span class="text-gray-600 text-sm flex-shrink-0">{{ value.key }}</span>
        <span class="flex-1 border-b border-dotted border-slate-400 mx-2 relative bottom-0.5"></span>
        <span class="text-gray-900 font-medium text-sm text-right">{{ value.value }}</span>
      </div>
    </div>
  </div>
</template>