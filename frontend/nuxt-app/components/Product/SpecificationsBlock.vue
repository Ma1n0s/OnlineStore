<script setup lang="ts">
import type { Product } from '~/types/product.types'

defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="mb-8">
    <h3 class="text-xl font-bold mb-4">Техническое характеристики</h3>
    <div class="overflow-x-auto w-full">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Параметр</th>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Значение</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <!-- <template v-for="(category, name) in product?.specifications" :key="name"> -->
          <!-- <tr class="bg-gray-100">
              <td class="px-4 py-3 font-bold text-gray-900 text-sm uppercase border-b">{{ name }}</td>
              <td class="px-4 py-3 border-b"></td>
            </tr> -->
          <tr v-for="(value, key) in product?.specifications" :key="value.key + key">
            <td class="px-4 py-3 text-sm text-gray-600">{{ value.key }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ value.value }}</td>
          </tr>
          <!-- </template> -->
        </tbody>
      </table>
    </div>
  </div>
</template>
