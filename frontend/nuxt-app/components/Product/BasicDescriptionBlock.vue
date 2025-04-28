<script setup lang="ts">
import { ref } from 'vue'
import type { Product } from '~/types/product.types'

defineProps<{
  product: Product
}>()

const expanded = ref(false)
</script>

<template>
  <div v-if="product.description" class="bg-white rounded-lg mb-8">
    <h3 class="text-2xl font-semibold mb-4 text-gray-800">Описание товара</h3>
    <div
      class="text-gray-600 leading-relaxed text-base"
      :class="{ 'line-clamp-4': !expanded }"
      v-html="product.description"
    ></div>
    <button
      v-if="product.description && product.description.length > 300"
      @click="expanded = !expanded"
      class="mt-3 text-sm font-medium text-primary hover:text-primary-hover transition-colors"
    >
      {{ expanded ? 'Свернуть' : 'Читать далее' }}
    </button>
  </div>
</template>
