<script setup>
import { reactive, computed } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'

// Состояние приложения
const state = reactive({
	priceRange: {
		min: 0,
		max: 30000,
		currentMin: 0,
		currentMax: 30000,
		inputMin: 0,
		inputMax: 30000,
		minTimeout: null,
		maxTimeout: null,
	},
	ui: {
		showFilters: false,
		isGrid: true,
		visibleItems: 8,
		isLoading: false,
	},
	filters: {
		selectedBrands: [],
	},
	items: [
		{
			id: 1,
			code: '15640682',
			title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348',
			image: 'Categories/Instruments.png',
			price: 13290,
			oldPrice: 15990,
			discount: 17,
			stock: 100,
			brand: 'Ryobi',
		},
		{
			id: 2,
			code: '15640683',
			title: 'Дрель-шуруповерт Bosch GSR 12V-15 06019A8021',
			image: 'Categories/Instruments.png',
			price: 14290,
			oldPrice: 16990,
			discount: 16,
			stock: 50,
			brand: 'Bosch',
		},
		{
			id: 3,
			code: '15640684',
			title: 'Шуруповерт Makita DF457DWE 165024-8',
			image: 'Categories/Instruments.png',
			price: 15290,
			oldPrice: 17990,
			discount: 15,
			stock: 30,
			brand: 'Makita',
		},
		{
			id: 4,
			code: '15640685',
			title: 'Шуруповерт DeWalt DCD771C2',
			image: 'Categories/Instruments.png',
			price: 18990,
			oldPrice: 21990,
			discount: 14,
			stock: 25,
			brand: 'DeWalt',
		},
		{
			id: 5,
			code: '15640686',
			title: 'Шуруповерт Metabo BS 18 LTX 600129700',
			image: 'Categories/Instruments.png',
			price: 16290,
			oldPrice: 18990,
			discount: 14,
			stock: 20,
			brand: 'Metabo',
		},
		{
			id: 6,
			code: '15640687',
			title: 'Шуруповерт Hitachi DS18DSAL',
			image: 'Categories/Instruments.png',
			price: 13990,
			oldPrice: 16990,
			discount: 18,
			stock: 15,
			brand: 'Hitachi',
		},
		{
			id: 7,
			code: '15640688',
			title: 'Шуруповерт AEG BSB 12C2-120X',
			image: 'Categories/Instruments.png',
			price: 14990,
			oldPrice: 17990,
			discount: 17,
			stock: 10,
			brand: 'AEG',
		},
		{
			id: 8,
			code: '15640689',
			title: 'Шуруповерт Black+Decker BDCDD12K',
			image: 'Categories/Instruments.png',
			price: 8990,
			oldPrice: 11990,
			discount: 25,
			stock: 40,
			brand: 'Black+Decker',
		},
	],
})

// Вычисляемые свойства
const filteredItems = computed(() => {
	return state.items.filter(item => {
		const priceMatch = item.price >= state.priceRange.currentMin && item.price <= state.priceRange.currentMax
		const brandMatch = state.filters.selectedBrands.length === 0 || state.filters.selectedBrands.includes(item.brand)
		return priceMatch && brandMatch
	})
})

const visibleItems = computed(() => filteredItems.value.slice(0, state.ui.visibleItems))

// Методы
const loadMoreItems = () => {
	if (state.ui.isLoading) return
	state.ui.isLoading = true

	setTimeout(() => {
		const newItems = [
			{
				id: state.items.length + 1,
				code: '15640690',
				title: 'Шуруповерт Hilti SF 6H-A22',
				image: 'Categories/Instruments.png',
				price: 24990,
				oldPrice: 28990,
				discount: 14,
				stock: 5,
				brand: 'Hilti',
			},
			{
				id: state.items.length + 2,
				code: '15640691',
				title: 'Шуруповерт Milwaukee M18 BPS-0',
				image: 'Categories/Instruments.png',
				price: 21990,
				oldPrice: 25990,
				discount: 15,
				stock: 8,
				brand: 'Milwaukee',
			},
		]
		state.items = [...state.items, ...newItems]
		state.ui.visibleItems += 2
		state.ui.isLoading = false
	}, 1000)
}

const toggleFilters = () => {
	state.ui.showFilters = !state.ui.showFilters
}

const showGrid = () => {
	state.ui.isGrid = true
}

const showList = () => {
	state.ui.isGrid = false
}

const updateMinPrice = value => {
	state.priceRange.currentMin = Math.min(Number(value), state.priceRange.currentMax - 1)
	state.priceRange.inputMin = state.priceRange.currentMin
}

const updateMaxPrice = value => {
	state.priceRange.currentMax = Math.max(Number(value), state.priceRange.currentMin + 1)
	state.priceRange.inputMax = state.priceRange.currentMax
}

const handleMinPriceInput = value => {
	if (!isNaN(value)) {
		state.priceRange.inputMin = value

		if (state.priceRange.minTimeout) clearTimeout(state.priceRange.minTimeout)

		state.priceRange.minTimeout = setTimeout(() => {
			state.priceRange.currentMin = Math.min(Math.max(value, state.priceRange.min), state.priceRange.currentMax - 1)
		}, 500)
	}
}

const handleMaxPriceInput = value => {
	if (!isNaN(value)) {
		state.priceRange.inputMax = value

		if (state.priceRange.maxTimeout) clearTimeout(state.priceRange.maxTimeout)

		state.priceRange.maxTimeout = setTimeout(() => {
			state.priceRange.currentMax = Math.max(Math.min(value, state.priceRange.max), state.priceRange.currentMin + 1)
		}, 500)
	}
}

const updateMinPriceFromInput = value => {
	if (!isNaN(value)) {
		state.priceRange.currentMin = Math.min(Math.max(value, state.priceRange.min), state.priceRange.currentMax - 1)
		state.priceRange.inputMin = value
	}
}

const updateMaxPriceFromInput = value => {
	if (!isNaN(value)) {
		state.priceRange.currentMax = Math.max(Math.min(value, state.priceRange.max), state.priceRange.currentMin + 1)
		state.priceRange.inputMax = value
	}
}

const handleSliderChange = type => {
	if (type === 'min') {
		state.priceRange.inputMin = state.priceRange.currentMin
	} else if (type === 'max') {
		state.priceRange.inputMax = state.priceRange.currentMax
	}
}

const resetPrice = () => {
	state.priceRange.currentMin = state.priceRange.min
	state.priceRange.currentMax = state.priceRange.max
	state.priceRange.inputMin = state.priceRange.min
	state.priceRange.inputMax = state.priceRange.max
	state.filters.selectedBrands = []
}

const toggleBrand = brand => {
	if (state.filters.selectedBrands.includes(brand)) {
		state.filters.selectedBrands = state.filters.selectedBrands.filter(b => b !== brand)
	} else {
		state.filters.selectedBrands = [...state.filters.selectedBrands, brand]
	}
}
</script>

<template>
	<div class="container mx-auto px-4 md:px-6 lg:px-8 py-6">
		<!-- Хлебные крошки -->
		<Breadcrumbs :list="breadcrumbs" />

		<!-- Категории -->
		<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
			<div
				v-for="i in 4"
				:key="i"
				class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden group"
			>
				<div class="p-4">
					<div class="font-bold text-gray-900 group-hover:text-red-600 transition-colors">Шуруповерты</div>
					<p class="text-gray-500 text-sm mt-1">4 754 товара</p>
					<NuxtImg
						src="Categories/Instruments.png"
						alt="Шуруповерты"
						class="w-full h-32 object-contain mt-3"
						width="300"
						height="300"
						loading="lazy"
						format="webp"
					/>
				</div>
			</div>
		</div>

		<!-- Заголовок результатов -->
		<div
			class="flex flex-col md:flex-row items-start md:items-center justify-between bg-white rounded-lg shadow-sm p-4 mb-6"
		>
			<p class="text-gray-700 mb-3 md:mb-0">
				Найдено <span class="font-semibold">{{ filteredItems.length }} товара</span>
			</p>

			<div class="flex items-center space-x-4">
				<div class="flex items-center">
					<span class="text-gray-700 mr-2">Сортировать по:</span>
					<select
						class="bg-gray-50 border border-gray-300 text-gray-700 rounded-lg px-3 py-1 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
					>
						<option>По популярности</option>
						<option>Отзывам</option>
						<option>Рейтингу</option>
						<option>Цене</option>
					</select>
				</div>

				<div class="flex items-center space-x-1">
					<button
						@click="showGrid"
						:class="{ 'bg-gray-100 text-red-600': state.ui.isGrid }"
						class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
					>
						<Icon name="tabler:layout-grid-filled" width="20" height="20" class="h-7 w-7" />
					</button>
					<button
						@click="showList"
						:class="{ 'bg-gray-100 text-red-600': !state.ui.isGrid }"
						class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
					>
						<Icon name="tabler:layout-filled" width="20" height="20" class="h-7 w-7" />
					</button>
				</div>
			</div>
		</div>

		<!-- Основной контент -->
		<div class="flex flex-col lg:flex-row gap-6">
			<!-- Боковая панель фильтров -->
			<div class="w-full lg:w-1/4">
				<div class="bg-white rounded-lg shadow-sm p-5 sticky top-4">
					<div class="mb-6">
						<div class="flex items-center text-gray-700 mb-2">
							<NuxtImg src="arrow-left.svg" alt="Назад" width="16" height="16" class="h-4 w-4 mr-2" />
							<span>Инструмент</span>
						</div>
						<div class="bg-gray-50 p-3 rounded-lg">
							<span class="font-medium">Шуруповерты</span>
						</div>
					</div>

					<!-- Фильтр по цене -->
					<div class="mb-6">
						<div class="flex justify-between items-center mb-3">
							<h3 class="font-semibold text-gray-900">Цена, ₽</h3>
							<button @click="resetPrice" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
								Сбросить
							</button>
						</div>

						<div class="flex items-center space-x-3 mb-4">
							<div class="relative flex-1">
								<TextInput
									:modelValue="state.priceRange.inputMin"
									@update:modelValue="handleMinPriceInput"
									@keyup.enter="updateMinPriceFromInput"
									class="w-full"
								/>
							</div>
							<span class="text-gray-400">—</span>
							<div class="relative flex-1">
								<TextInput
									:modelValue="state.priceRange.inputMax"
									@update:modelValue="handleMaxPriceInput"
									@keyup.enter="updateMaxPriceFromInput"
									class="w-full"
								/>
							</div>
						</div>

						<div class="px-2">
							<div class="relative h-8">
								<div class="absolute w-full h-1 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
								<div
									class="absolute h-1 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
									:style="{
										left: `${
											((state.priceRange.currentMin - state.priceRange.min) /
												(state.priceRange.max - state.priceRange.min)) *
											100
										}%`,
										width: `${
											((state.priceRange.currentMax - state.priceRange.currentMin) /
												(state.priceRange.max - state.priceRange.min)) *
											100
										}%`,
									}"
								></div>
								<input
									type="range"
									:min="state.priceRange.min"
									:max="state.priceRange.max"
									v-model.number="state.priceRange.currentMin"
									@input="handleSliderChange('min')"
									class="absolute w-full appearance-none pointer-events-none"
								/>
								<input
									type="range"
									:min="state.priceRange.min"
									:max="state.priceRange.max"
									v-model.number="state.priceRange.currentMax"
									@input="handleSliderChange('max')"
									class="absolute w-full appearance-none pointer-events-none"
								/>
							</div>
						</div>
					</div>

					<!-- Фильтр по брендам -->
					<div class="mb-6">
						<h3 class="font-semibold text-gray-900 mb-3">Производители</h3>
						<div class="space-y-2 max-h-60 overflow-y-auto pr-2">
							<label
								v-for="brand in ['Ryobi', 'Bosch', 'Makita', 'DeWalt', 'Metabo', 'Hitachi', 'AEG', 'Black+Decker']"
								:key="brand"
								class="flex items-center space-x-2 py-1 hover:bg-gray-50 px-2 rounded cursor-pointer"
								@click="toggleBrand(brand)"
							>
								<input
									type="checkbox"
									:checked="state.filters.selectedBrands.includes(brand)"
									class="rounded text-red-600 focus:ring-red-500 border-gray-300"
									@change="toggleBrand(brand)"
								/>
								<span class="text-gray-700">{{ brand }}</span>
							</label>
						</div>
					</div>

					<!-- Кнопки фильтров -->
					<div class="space-y-3">
						<button
							@click="toggleFilters"
							class="w-full flex items-center justify-center space-x-2 border border-gray-300 rounded-xl py-2 px-4 hover:bg-gray-50 transition-colors"
						>
							<Icon name="tabler:filter" class="h-6 w-6" />
							<span>Все фильтры</span>
						</button>
						<button
							class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 px-4 transition-colors font-medium"
						>
							Показать {{ filteredItems.length }} товаров
						</button>
					</div>
				</div>
			</div>

			<!-- Список товаров -->
		</div>

		<transition name="fade">
			<div v-if="state.ui.showFilters" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="toggleFilters"></div>
		</transition>

		<transition name="slide">
			<div v-if="state.ui.showFilters" class="fixed inset-y-0 left-0 w-80 bg-white z-50 p-6 overflow-y-auto shadow-xl">
				<div class="flex justify-between items-center mb-6">
					<h2 class="text-xl font-bold text-gray-900">Все фильтры</h2>
					<button @click="toggleFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
						<Icon name="tabler:xbox-x-filled" class="h-6 w-6" />
					</button>
				</div>

				<div class="space-y-6">
					<div>
						<h3 class="font-semibold text-gray-900 mb-3">Цена, ₽</h3>
						<div class="flex items-center space-x-3 mb-4">
							<TextInput
								:modelValue="state.priceRange.inputMin"
								@update:modelValue="handleMinPriceInput"
								@keyup.enter="updateMinPriceFromInput"
								class="w-full"
							/>
							<span class="text-gray-400">—</span>
							<TextInput
								:modelValue="state.priceRange.inputMax"
								@update:modelValue="handleMaxPriceInput"
								@keyup.enter="updateMaxPriceFromInput"
								class="w-full"
							/>
						</div>
						<div class="px-2">
							<div class="relative h-8">
								<div class="absolute w-full h-1 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
								<div
									class="absolute h-1 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
									:style="{
										left: `${
											((state.priceRange.currentMin - state.priceRange.min) /
												(state.priceRange.max - state.priceRange.min)) *
											100
										}%`,
										width: `${
											((state.priceRange.currentMax - state.priceRange.currentMin) /
												(state.priceRange.max - state.priceRange.min)) *
											100
										}%`,
									}"
								></div>
								<input
									type="range"
									:min="state.priceRange.min"
									:max="state.priceRange.max"
									v-model.number="state.priceRange.currentMin"
									@input="handleSliderChange('min')"
									class="absolute w-full appearance-none pointer-events-none"
								/>
								<input
									type="range"
									:min="state.priceRange.min"
									:max="state.priceRange.max"
									v-model.number="state.priceRange.currentMax"
									@input="handleSliderChange('max')"
									class="absolute w-full appearance-none pointer-events-none"
								/>
							</div>
						</div>
					</div>

					<div>
						<h3 class="font-semibold text-gray-900 mb-3">Производители</h3>
						<div class="space-y-2 max-h-60 overflow-y-auto pr-2">
							<label
								v-for="brand in ['Ryobi', 'Bosch', 'Makita', 'DeWalt', 'Metabo', 'Hitachi', 'AEG', 'Black+Decker']"
								:key="brand"
								class="flex items-center space-x-2 py-1 hover:bg-gray-50 px-2 rounded cursor-pointer"
								@click="toggleBrand(brand)"
							>
								<input
									type="checkbox"
									:checked="state.filters.selectedBrands.includes(brand)"
									class="rounded text-red-600 focus:ring-red-500 border-gray-300"
									@change="toggleBrand(brand)"
								/>
								<span class="text-gray-700">{{ brand }}</span>
							</label>
						</div>
					</div>
					<button
						@click="toggleFilters"
						class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-3 px-4 transition-colors duration-200 font-medium shadow-md hover:shadow-lg active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
					>
						Показать {{ filteredItems.length }} товаров
					</button>
				</div>
			</div>
		</transition>
	</div>
</template>

<style scoped>
input[type='range']::-webkit-slider-thumb {
	-webkit-appearance: none;
	appearance: none;
	width: 18px;
	height: 18px;
	background: #dc2626;
	border-radius: 50%;
	cursor: pointer;
	border: 2px solid white;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type='range']::-moz-range-thumb {
	width: 18px;
	height: 18px;
	background: #dc2626;
	border-radius: 50%;
	cursor: pointer;
	border: 2px solid white;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type='range'] {
	-webkit-appearance: none;
	appearance: none;
	height: 8px;
	width: 100%;
	position: absolute;
	top: 0;
	left: 0;
	background: transparent;
	pointer-events: none;
}

input[type='range']::-webkit-slider-runnable-track {
	@apply bg-transparent h-1 rounded-full;
}

input[type='range']::-webkit-slider-thumb {
	@apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
	transform: translateY(-50%);
	position: relative;
	z-index: 10;
	top: 50%;
	margin-top: 8px;
}

input[type='range']::-moz-range-track {
	@apply bg-transparent h-1 rounded-full;
}

input[type='range']::-moz-range-thumb {
	@apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
	position: relative;
	z-index: 10;
	top: 50%;
	margin-top: 2px;
}

input[type='range']:first-of-type::-webkit-slider-thumb {
	z-index: 20;
}

input[type='range']:last-of-type::-webkit-slider-thumb {
	z-index: 15;
}

.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
	opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
	transition: transform 0.3s ease;
}
.slide-enter,
.slide-leave-to {
	transform: translateX(-100%);
}

.line-clamp-2 {
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}
</style>
