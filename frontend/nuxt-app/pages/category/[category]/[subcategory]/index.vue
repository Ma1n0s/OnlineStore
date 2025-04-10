<script setup>
const route = useRoute()

useHead({
  title: `${route.params.subsubcategory} | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты для строительства и ремонта, категория ${route.params.subsubcategory}`,
    },
  ],
})

import { ref, reactive, computed } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import Filter from '~/components/ProductRibbon/Filter.vue'
import ProductCartRibbon from '~/components/ProductRibbon/ProductCartRibbon.vue'

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
    visibleItems: 10,
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
    {
      id: 9,
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
      id: 10,
      code: '15640691',
      title: 'Шуруповерт Milwaukee M18 BPS-0',
      image: 'Categories/Instruments.png',
      price: 21990,
      oldPrice: 25990,
      discount: 15,
      stock: 8,
      brand: 'Milwaukee',
    },
  ],
})

const visibleItems = computed(() => {
  return state.items
    .filter(item => {
      const priceMatch = item.price >= state.priceRange.currentMin && item.price <= state.priceRange.currentMax
      const brandMatch = state.filters.selectedBrands.length === 0 || state.filters.selectedBrands.includes(item.brand)
      return priceMatch && brandMatch
    })
    .slice(0, state.ui.visibleItems)
})

const loadMoreItems = () => {
  state.ui.visibleItems += 10
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

const breadcrumbs = ref([{ url: '/category', name: 'Каталог', color: '#6b7280' }, { name: 'Шуруповерты' }])

const toggleBrand = brand => {
  if (state.filters.selectedBrands.includes(brand)) {
    state.filters.selectedBrands = state.filters.selectedBrands.filter(b => b !== brand)
  } else {
    state.filters.selectedBrands = [...state.filters.selectedBrands, brand]
  }
}

const catalogDescription = ref({
  title: 'Шуруповерты',
  description: 'Широкий выбор шуруповертов от ведущих производителей',
})
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-6">
    <Breadcrumbs :list="breadcrumbs" />

    <CategoryDescription :data="catalogDescription" />

    <div
      class="flex flex-col md:flex-row items-start md:items-center justify-between bg-white rounded-lg shadow-sm p-4 mb-6"
    >
      <p class="text-gray-700 mb-3 md:mb-0">
        Найдено
        <span class="font-semibold"
          >{{
            state.items.filter(item => {
              const priceMatch = item.price >= state.priceRange.currentMin && item.price <= state.priceRange.currentMax
              const brandMatch =
                state.filters.selectedBrands.length === 0 || state.filters.selectedBrands.includes(item.brand)
              return priceMatch && brandMatch
            }).length
          }}
          товара</span
        >
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
            <Icon name="tabler:layout-grid-filled" class="h-5 w-5" />
          </button>
          <button
            @click="showList"
            :class="{ 'bg-gray-100 text-red-600': !state.ui.isGrid }"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <Icon name="tabler:layout-list-filled" class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
      <Filter
        :state="state"
        @toggleFilters="toggleFilters"
        @resetPrice="resetPrice"
        @toggleBrand="toggleBrand"
        @handleMinPriceInput="handleMinPriceInput"
        @handleMaxPriceInput="handleMaxPriceInput"
        @updateMinPriceFromInput="updateMinPriceFromInput"
        @updateMaxPriceFromInput="updateMaxPriceFromInput"
        @handleSliderChange="handleSliderChange"
      />

      <ProductCartRibbon :items="visibleItems" :isGrid="state.ui.isGrid" @loadMore="loadMoreItems" />
    </div>

    <transition name="fade">
      <div v-if="state.ui.showFilters" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="toggleFilters"></div>
    </transition>

    <transition name="slide">
      <div v-if="state.ui.showFilters" class="fixed inset-y-0 left-0 w-80 bg-white z-50 p-6 overflow-y-auto shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-900">Все фильтры</h2>
          <button @click="toggleFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
            <NuxtImg src="Krestiks.svg" alt="Закрыть" width="24" height="24" class="h-6 w-6" />
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
                v-for="brand in [
                  'Ryobi',
                  'Bosch',
                  'Makita',
                  'DeWalt',
                  'Metabo',
                  'Hitachi',
                  'AEG',
                  'Black+Decker',
                  'Hilti',
                  'Milwaukee',
                ]"
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
            Показать
            {{
              state.items.filter(item => {
                const priceMatch =
                  item.price >= state.priceRange.currentMin && item.price <= state.priceRange.currentMax
                const brandMatch =
                  state.filters.selectedBrands.length === 0 || state.filters.selectedBrands.includes(item.brand)
                return priceMatch && brandMatch
              }).length
            }}
            товаров
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>
