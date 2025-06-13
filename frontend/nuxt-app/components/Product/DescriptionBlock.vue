<script setup lang="ts">
import type { Product } from '~/types/product.types'
defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="bg-white rounded-lg p-6 h-full flex flex-col">
    <h2 class="text-sm font-bold mb-4">характеристики:</h2>
    <div class="flex-1">
      <ul class="space-y-3">
        <li 
          v-for="value in product.specificationsB" 
          :key="value.name" 
          class="grid grid-cols-1 min-w-0"
        >
          <div class="flex items-baseline min-w-0">
            <span class="text-gray-500 text-sm truncate min-w-0">
              {{ value.name }}
            </span>
            <span class="flex-1 border-b border-dotted border-slate-400 mx-2 relative bottom-0.5"></span>
            <span class="font-medium text-sm whitespace-nowrap">
              {{ value.value }}
            </span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>