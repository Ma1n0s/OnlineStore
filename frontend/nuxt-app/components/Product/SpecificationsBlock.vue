<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Partial<Product>>(productData)
</script>
<template>
  <div class="mb-8">
    <h3 class="text-xl font-bold mb-4">Технические характеристики</h3>
    <div class="overflow-x-auto w-full">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Параметр</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Значение</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <template v-for="(category, name) in product?.specifications" :key="name">
            <tr class="bg-gray-100">
              <td class="px-4 py-3 font-bold text-gray-900 text-sm uppercase border-b border-gray-200">
                {{ name }}
              </td>
              <td class="px-4 py-3 border-b border-gray-200"></td>
            </tr>
            <tr v-for="(title, value) in category" :key="title">
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ value }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ title }}</td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>
