<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
</script>

<template>
  <div v-if="product.description" class="bg-white rounded-lg mb-8">
    <h3 class="text-2xl font-semibold mb-4 text-gray-800">Описание товара</h3>
    <div class="text-gray-600 leading-relaxed text-base" :class="{ 'line-clamp-4': !expanded }">
      {{ product.description }}
    </div>
    <button
      v-if="product.description.length > 300"
      @click="expanded = !expanded"
      class="mt-3 text-sm font-medium text-primary hover:text-primary-hover transition-colors"
    >
      {{ expanded ? 'Свернуть' : 'Читать далее' }}
    </button>
  </div>
</template>

<!-- <script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
</script>
<template>
	<div v-if="product.description" class="mb-8">
		<h3 class="text-2xl font-bold mb-4">Описание</h3>
		<div class="text-xs leading-relaxed">
			{{ product.description.slice(0, 1000) }}
		</div>
	</div>
</template> -->
