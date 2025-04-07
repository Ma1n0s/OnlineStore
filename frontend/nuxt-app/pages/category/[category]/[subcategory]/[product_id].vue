<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { productData } from '~/shared/productData'
import type { Product } from '~/types/product.types'

import DescriptionBlock from '~/components/Product/DescriptionBlock.vue'
import SpecificationsBlock from '~/components/Product/SpecificationsBlock.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'

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

const toggleFavorite = async () => {
	isFavorite.value = !isFavorite.value
}

const breadcrumbs = ref([
	{
		url: '/',
		name: 'Главная',
		color: '#',
	},
	{ url: '/catalog', name: 'Каталог', color: '#6b7280' },
	{ url: `/catalog/${product.category?.slug}`, name: product.category?.name || 'Категория', color: '#6b7280' },
	{ url: '', name: product.name || 'Товар', color: '#000000' },
])
//типы для typescr интерфейсы
</script>

<template>
	<div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
		<Breadcrumbs :list="breadcrumbs" />

		<div v-if="!loading">
			<h2 class="font-bold text-2xl mb-2">{{ product.title }}</h2>
			<div class="flex flex-wrap gap-4 mb-6">
				<p class="text-sm" v-if="product.code">Код товара: {{ product.code }}</p>
				<div class="flex items-center gap-2" v-if="product.reviewsCount || product.questionsCount">
					<Icon v-for="i in 5" :key="i" name="tabler:star" class="w-6 h-6" />
					<p class="text-sm">
						<span>10 отзывов</span>
						<span> | </span>
						<span>5 вопросов</span>
					</p>
				</div>

				<div class="flex items-center gap-2" v-if="product.warranty">
					<Icon name="tabler:clipboard-smile" class="w-6 h-6" />
					<p class="text-sm">Гарантия производителя {{ product.warranty }}</p>
				</div>

				<div class="flex items-center gap-2 cursor-pointer" @click="toggleFavorite">
					<Icon :name="isFavorite ? 'tabler:heart-filled' : 'tabler:heart'" class="w-6 h-6" />
					<p class="text-sm">{{ isFavorite ? 'В избранном' : 'В избранное' }}</p>
				</div>
			</div>

			<div class="flex flex-col md:flex-row gap-8">
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

				<!-- Характеристики и описание -->
				<div class="w-full md:w-3/5">
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
									<p v-if="product.price.savings" class="text-primary font-medium">
										Экономия {{ product.price.savings }} ₽
									</p>
								</div>
							</div>

							<div class="flex flex-col sm:flex-row gap-3">
								<button class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition">
									В корзину
								</button>
							</div>
						</div>
					</div>
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
						<div v-if="product.description" class="mb-8">
							<h3 class="text-2xl font-bold mb-4">Описание</h3>
							<div class="text-xs leading-relaxed">
								{{ product.description.slice(0, 1000) }}
							</div>
						</div>

						<SpecificationsBlock />
					</div>

					<!-- Боковая панель с брендом -->
					<div class="sticky top-4 h-fit">
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
					</div>
				</div>
			</div>
		</div>

		<div v-else class="flex justify-center items-center h-64">
			<div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
		</div>
	</div>
</template>
