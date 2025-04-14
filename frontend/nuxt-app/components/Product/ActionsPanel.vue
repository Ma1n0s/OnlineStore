<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
const isFavorite = ref(false)

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value
}

onMounted(() => {
  isFavorite.value = false
  console.log(product.specifications)
})
</script>

<template>
  <div class="mb-2">
    <h1 class="text-3xl font-bold text-gray-900 px-2 ">{{ product.name }}</h1>
    <div v-if="product.shortDescription" class="text-lg text-gray-600">
      {{ product.shortDescription }}
    </div>
  </div>

  <div class="flex flex-wrap items-center gap-6 mb-8">
    <!-- Код товара -->
    <div v-if="product.code" class="flex items-center gap-2  py-2 bg-gray-50 rounded-lg">
      <p class="text-sm text-gray-600">
        Код: <span class="font-medium text-gray-800">{{ product.code }}</span>
      </p>
    </div>

    <!-- Рейтинг и отзывы -->
    <div
      v-if="product.reviewsCount || product.questionsCount"
      class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg"
    >
      <div class="flex items-center gap-1">
        <Icon v-for="i in 5" :key="i" name="tabler:star-filled" class="w-5 h-5 text-yellow-400" />
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <span class="font-medium text-gray-800">{{ product.rating || '4.8' }}</span>
        <span class="w-px h-4 bg-gray-300"></span>
        <span class="hover:underline cursor-pointer">{{ product.reviewsCount || 10 }} отзывов</span>
        <span class="w-px h-4 bg-gray-300"></span>
        <span class="hover:underline cursor-pointer">{{ product.questionsCount || 5 }} вопросов</span>
      </div>
    </div>

    <!-- Гарантия -->
    <div v-if="product.warranty" class="flex items-center gap-2 px-3 py-2 bg-blue-50 rounded-lg">
      <Icon name="tabler:shield-check" class="w-5 h-5 text-blue-500" />
      <p class="text-sm text-gray-600">
        Гарантия <span class="font-medium text-blue-600">{{ product.warranty }}</span>
      </p>
    </div>

    <!-- Избранное -->
    <div
      class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors cursor-pointer"
      :class="[isFavorite ? 'bg-red-50' : 'bg-gray-50 hover:bg-gray-100']"
      @click="toggleFavorite"
    >
      <Icon
        :name="isFavorite ? 'tabler:heart-filled' : 'tabler:heart'"
        class="w-5 h-5 transition-colors"
        :class="[isFavorite ? 'text-red-500' : 'text-gray-500 hover:text-red-400']"
      />
      <p class="text-sm transition-colors" :class="[isFavorite ? 'text-red-600 font-medium' : 'text-gray-600']">
        {{ isFavorite ? 'В избранном' : 'В избранное' }}
      </p>
    </div>
  </div>
</template>