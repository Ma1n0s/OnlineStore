<script setup lang="ts">
import type { Product } from '~/types/product.types'
defineProps<{
  product: Product
}>()
</script>

<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Основные характеристики</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Первая колонка -->
      <div class="space-y-6">
        <div>
          <!-- <h3 class="font-semibold text-gray-700 mb-2">Основные</h3> -->
          <ul class="space-y-3">
            <li v-for="value in product.mainSpecifications" :key="value.name" class="flex justify-between">
              <span class="text-gray-500">{{ value.name }}</span>
              <span class="font-medium">{{ value.value }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Вторая колонка -->
      <!-- <div class="space-y-6">
        <div>
          <h3 class="font-semibold text-gray-700 mb-2">Дополнительные характеристики</h3>
          <ul class="space-y-3">
            <li v-for="(value, key) in product.mainSpecifications" :key="key" class="flex justify-between">
              <span class="text-gray-500">{{ key.toString().replace(' ', ' ') }}</span>
              <span class="font-medium">{{ value }}</span>
            </li>
          </ul>
        </div>
      </div> -->
    </div>
  </div>
</template>
