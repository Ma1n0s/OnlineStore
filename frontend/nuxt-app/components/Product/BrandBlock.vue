<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
</script>
<template>
	<div class="rounded-lg p-6 bg-gray-50">
		<h3 class="text-2xl font-bold mb-4">{{ product.brand }}</h3>
		<p class="text-gray-600 mb-6 text-gray">Все товары бренда</p>

		<NuxtLink
			to="#"
			class="block w-full bg-second hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium text-center transition"
		>
			Смотреть все товары
		</NuxtLink>
	</div>
</template>
