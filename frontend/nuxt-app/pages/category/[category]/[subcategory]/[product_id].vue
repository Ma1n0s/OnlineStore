<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

import DescriptionBlock from '~/components/Product/DescriptionBlock.vue'
import SpecificationsBlock from '~/components/Product/SpecificationsBlock.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import ImageBlock from '~/components/Product/ImageBlock.vue'
import BasicDescriptionBlock from '~/components/Product/BasicDescriptionBlock.vue'
import BrandBlock from '~/components/Product/BrandBlock.vue'
import Basket from '~/components/Product/Basket.vue'
import ActionsPanel from '~/components/Product/ActionsPanel.vue'

const route = useRoute()

// @ts-ignore
const product = reactive<Product>(productData)

// const { data, status, error, refresh, clear } = await useFetch<Product>(
//   `http://127.0.0.1:8000/api/products/${route.params.product_id}`
// );

// if (data.value) {
//   Object.assign(product, data.value);
//   console.log(JSON.stringify(product));
// }

// console.log(status.value);
// console.log(error.value);

// Вкладки
const tabs = ref([
	{ id: 'description', title: 'ОПИСАНИЕ И ХАРАКТЕРИСТИКИ' },
	// { id: "reviews", title: "ОТЗЫВЫ" },
	// { id: "questions", title: "ВОПРОСЫ И ОТВЕТЫ" },
])

const activeTab = ref('description')
const isFavorite = ref(false)
const loading = ref(true)
const activeImage = ref(product?.images?.[0]?.src || '')

onMounted(() => {
	activeTab.value = 'description'
	isFavorite.value = false
	loading.value = false

	console.log(product.specifications)
})

const breadcrumbs = ref([
	{ url: '/category', name: 'Каталог', color: '#6b7280' },
	{ url: `/category/${product.category?.slug}`, name: product.category?.name || 'Категория', color: '#6b7280' },
	{ url: '', name: product.name || 'Товар', color: '#000000' },
])
//типы для typescr интерфейсы
</script>

<template>
	<div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
		<Breadcrumbs :list="breadcrumbs" />

		<div v-if="!loading">
			<h2 class="font-bold text-2xl mb-2">{{ product.title }}</h2>
			<ActionsPanel />
			<div class="flex flex-col md:flex-row gap-8">
				<!-- Свой Свайпер по img -->
				<ImageBlock />

				<!-- Характеристики и описание -->
				<div class="w-full md:w-3/5">
					<Basket />
					<!-- Основные характеристики -->
					<DescriptionBlock />
				</div>
			</div>

			<div class="border-b border-gray-200 mb-8">
				<nav class="flex space-x-6">
					<button
						v-for="tab in tabs"
						:key="tab.id"
						@click="activeTab = tab.id"
						class="py-4 px-1 border-b-2 font-medium text-sm"
						:class="{
							'border-second text-second': activeTab === tab.id,
							'border-transparent text-gray hover:text-dark hover:border-gray-300': activeTab !== tab.id,
						}"
					>
						{{ tab.title }}
					</button>
				</nav>
			</div>

			<div>
				<div v-show="activeTab === 'description'" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
					<!-- Основной контент (описание и характеристики) -->
					<div class="lg:col-span-3">
						<!-- Описание -->
						<BasicDescriptionBlock />
						<!-- Техническое описание -->
						<SpecificationsBlock />
					</div>

					<!-- Боковая панель с брендом -->
					<div class="sticky top-4 h-fit">
						<BrandBlock />
					</div>
				</div>
			</div>
		</div>

		<div v-else class="flex justify-center items-center h-64">
			<div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
		</div>
	</div>
</template>
