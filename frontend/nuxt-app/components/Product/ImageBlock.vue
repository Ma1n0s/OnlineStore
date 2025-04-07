<script setup lang="ts">
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

const product = reactive<Product>(productData)
const activeImage = ref(product?.images?.[0]?.src || '')
</script>
<template>
	<div class="w-full md:w-2/5">
		<div class="bg-white rounded-lg shadow-md p-4">
			<NuxtImg :src="activeImage" :alt="product.name" class="w-full h-80 object-contain mb-4" />
			<div class="flex gap-2 overflow-x-auto">
				<NuxtImg
					v-for="(image, index) in product.images"
					:key="index"
					:src="image.src"
					:alt="'Изображение ' + (index + 1)"
					class="w-16 h-16 object-cover rounded cursor-pointer border"
					:class="{ 'border-primary': image.src === activeImage }"
					@click="activeImage = image.src"
				/>
			</div>
		</div>
	</div>
</template>
