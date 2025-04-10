<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
</script>
<template>
  <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col gap-4">
      <div>
        <h2 class="text-xl font-bold text-second mb-2">Сезон впереди</h2>
        <div class="flex items-center gap-4">
          <p class="text-3xl font-bold">{{ product.price.final }} ₽</p>
          <p
            v-if="product.price.original && product.price.original !== product.price.final"
            class="text-lg line-through text-gray"
          >
            {{ product.price.original }} ₽
          </p>
          <p v-if="product.price.savings" class="text-primary font-medium">Экономия {{ product.price.savings }} ₽</p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3">
        <button class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition">
          В корзину
        </button>
        <button class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition">
          В аренду
        </button>
      </div>
    </div>
  </div>
</template>
