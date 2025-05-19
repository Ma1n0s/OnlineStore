<script setup lang="ts">
import type { Product } from '~/types/product.types'
defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="bg-white rounded-lg p-6 h-full flex flex-col">
    <h2 class="text-sm font-bold mb-4">характеристики:</h2>
    <div class="flex-1 overflow-y-auto pr-2">
      <ul class="space-y-3">
        <li 
          v-for="value in product.mainSpecifications" 
          :key="value.name" 
          class="flex justify-between gap-4"
        >
          <span class="text-gray-500 text-sm flex-shrink-0">
            {{ value.name }}
          </span>
          <span class="font-medium text-right text-sm min-w-0">
            {{ value.value }}
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>

