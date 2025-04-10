<script setup lang="ts">
import { reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Partial<Product>>(productData)
</script>

<template>
	<div class="bg-white rounded-lg shadow-md p-6 mb-6">
		<h2 class="text-xl font-bold mb-4">Основные характеристики</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<!-- Первая колонка -->
			<div class="space-y-6">
				<div>
					<h3 class="font-semibold text-gray-700 mb-2">Основные</h3>
					<ul class="space-y-3">
						<li
							v-for="(value, key) in product.specifications['Основны характеристики']"
							:key="key"
							class="flex justify-between"
						>
							<span class="text-gray-500">{{ key.replace(' ', ' ') }}</span>
							<span class="font-medium">{{ value }}</span>
						</li>
					</ul>
				</div>
			</div>

			<!-- Вторая колонка -->
			<div class="space-y-6">
				<div>
					<h3 class="font-semibold text-gray-700 mb-2">Экран</h3>
					<ul class="space-y-3">
						<template v-for="key in ['Диагональ', 'Разрешение', 'Тип', 'Тип матрицы']">
							<li v-if="product.specifications['экран'][key]" :key="key" class="flex justify-between">
								<span class="text-gray-500">{{ key }}</span>
								<span class="font-medium">
									{{ product.specifications['экран'][key] }}
								</span>
							</li>
						</template>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>
