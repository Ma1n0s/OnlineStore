<script setup>
import { reactive, computed } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'

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
  popularTags: [
    "Пылесосы с розеткой для электроинструмента",
    "Дрели-шуруповерты для женщин",
    "Дрели-шуруповерты для мебели",
    "Противопожарное оборудование и Inventar",
    "Зеленые дрели-шуруповерты аккумуляторные",
    "Синие дрели-шуруповерты аккумуляторные",
    "Желтые дрели-шуруповерты аккумуляторные",
  ],

  articles: [
    {
      id: 1,
      title: "Советы по выбору электроинструмента",
      excerpt: "Хорошие результаты получать не просто. Различные ситуации требуют...",
      date: "05.09.2024",
    },
    {
      id: 2,
      title: "Электроинструмент - как правильно работать",
      excerpt: "Каждый, кто хоть раз держал в руке электроинструмент, непременно должен знать...",
      date: "05.09.2024",
    },
    {
      id: 3,
      title: "Электроинструмент нужен в каждом доме",
      excerpt: "Вряд ли найдутся люди, готовые поспорить с тем, что мелкий домашний ремонт...",
      date: "07.06.2023",
    },
    {
      id: 4,
      title: "Всегда ли работает традиционная схема выбора?",
      excerpt: "Все рекомендации по вопросу выбора электроинструмента начинаются с...",
      date: "07.06.2023",
    },
  ],

  products: [
    {
      name: "Шуруповерты",
      image: "Categories/Instruments.png",
    },
    {
      name: "Дрели",
      image: "Categories/Instruments.png",
    },
    {
      name: "Перфораторы",
      image: "Categories/Instruments.png",
    },
    {
      name: "Болгарки",
      image: "Categories/Instruments.png",
    },
    {
      name: "Пилы",
      image: "Categories/Instruments.png",
    },
    {
      name: "Фрезеры",
      image: "Categories/Instruments.png",
    },
    {
      name: "Шлифмашины",
      image: "Categories/Instruments.png",
    },
    {
      name: "Лобзики",
      image: "Categories/Instruments.png",
    },
    {
      name: "Строительные пылесосы",
      image: "Categories/Instruments.png",
    },
    {
      name: "Измерительные инструменты",
      image: "Categories/Instruments.png",
    },
    {
      name: "Краскопульты",
      image: "Categories/Instruments.png",
    },
    {
      name: "Тепловые пушки",
      image: "Categories/Instruments.png",
    },
  ],
});
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="#" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Инструмент</NuxtLink>
    </nav>

    <div>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Строительный инструмент</h1>
        </div>
        <NuxtLink to="#" class="text-primary hover:underline mt-2 md:mt-0">Как выбрать электроинструмент</NuxtLink>
      </div>

      <CategoryList class="mb-8" />

      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Часто ищут</h2>
        <div class="flex flex-wrap gap-2">
          <NuxtLink
            v-for="(tag, index) in state.popularTags"
            :key="index"
            to="#"
            class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
          >
            {{ tag }}
          </NuxtLink>
          <NuxtLink
            to="#"
            class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
          >
            ... Показать ещё
          </NuxtLink>
        </div>
      </div>

      <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Статьи</h2>
          <NuxtLink
            to="#"
            class="bg-gray-200 hover:bg-gray-300 rounded-md px-4 py-2 transition duration-200 ease-in-out"
          >
            Все статьи
          </NuxtLink>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <NuxtLink
            v-for="article in state.articles"
            :key="article.id"
            to="#"
            class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-200 ease-in-out"
          >
            <h3 class="font-semibold text-lg mb-2">{{ article.title }}</h3>
            <p class="text-gray-600 mb-4">{{ article.excerpt }}</p>
            <p class="text-gray-400 text-sm">{{ article.date }}</p>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>
