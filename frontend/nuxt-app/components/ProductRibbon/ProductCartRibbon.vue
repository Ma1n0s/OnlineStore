<script setup>
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

const filteredItems = computed(() => {
  return state.items.filter(item => {
    const priceMatch = item.price >= state.priceRange.currentMin && item.price <= state.priceRange.currentMax
    const brandMatch = state.filters.selectedBrands.length === 0 || state.filters.selectedBrands.includes(item.brand)
    return priceMatch && brandMatch
  })
})

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
</script>
<template>
  <div class="w-full lg:w-3/4">
    <div
      v-if="filteredItems.length > 0"
      :class="state.ui.isGrid ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5' : 'space-y-5'"
    >
      <div
        v-for="item in filteredItems.slice(0, state.ui.visibleItems)"
        :key="item.id"
        :class="
          state.ui.isGrid
            ? 'bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden flex flex-col h-full'
            : 'bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden flex'
        "
      >
        <div :class="state.ui.isGrid ? 'relative h-48 flex-shrink-0' : 'relative w-1/3 flex-shrink-0'">
          <NuxtImg
            :src="item.image"
            :alt="item.title"
            :class="state.ui.isGrid ? 'w-full h-full object-contain p-4' : 'w-full h-full object-cover'"
            width="300"
            height="300"
            loading="lazy"
            format="webp"
          />
          <button
            :class="state.ui.isGrid ? 'absolute top-3 right-3' : 'absolute top-3 right-3'"
            class="p-1 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors flex items-center justify-center"
          >
            <Icon name="tabler:heart" class="h-5 w-5" />
          </button>
          <div
            v-if="item.discount"
            :class="state.ui.isGrid ? 'absolute top-3 left-3' : 'absolute top-3 left-3'"
            class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded"
          >
            -{{ item.discount }}%
          </div>
        </div>

        <div :class="state.ui.isGrid ? 'p-4 flex flex-col flex-grow' : 'w-2/3 p-4 flex flex-col'">
          <div class="flex justify-between items-start mb-1">
            <span class="text-gray-500 text-xs">Код: {{ item.code }}</span>
          </div>

          <NuxtLink to="/category/instrument/wire/1" class="block">
            <h3 class="font-medium text-gray-900 hover:text-red-600 transition-colors line-clamp-2 mb-2 min-h-[2.5rem]">
              {{ item.title }}
            </h3>
          </NuxtLink>

          <p class="text-green-600 text-sm mb-3 flex items-center">В наличии > {{ item.stock }} шт.</p>

          <div class="mb-3 flex-grow flex items-end">
            <div>
              <span class="text-gray-400 line-through text-sm mr-2">{{ item.oldPrice.toLocaleString() }} ₽</span>
              <span class="text-red-600 font-bold text-lg">{{ item.price.toLocaleString() }} ₽</span>
            </div>
          </div>

          <button
            class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors font-medium"
          >
            В корзину
          </button>
        </div>
      </div>
    </div>

    <!-- Нет результатов -->
    <div v-else class="bg-white rounded-lg shadow-sm p-8 text-center">
      <NuxtImg src="/images/empty-state.png" alt="Товары не найдены" width="200" height="200" class="mx-auto" />
      <h3 class="mt-4 text-lg font-medium text-gray-900">Товары не найдены</h3>
      <p class="mt-1 text-gray-500">Попробуйте изменить параметры фильтрации</p>
      <button
        @click="resetPrice"
        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none"
      >
        Сбросить фильтры
      </button>
    </div>

    <!-- Кнопка "Показать еще" -->
    <div
      class="flex justify-center mt-8"
      v-if="state.ui.visibleItems < filteredItems.length && filteredItems.length > 0"
    >
      <button
        @click="loadMoreItems"
        :disabled="state.ui.isLoading"
        class="bg-white border border-red-600 text-red-600 hover:bg-red-50 py-2 px-6 rounded-lg transition-colors font-medium flex items-center"
      >
        <span v-if="!state.ui.isLoading">Показать ещё</span>
        <div
          v-if="state.ui.isLoading"
          class="animate-spin -ml-1 mr-2 h-5 w-5 text-red-600"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          4444444444
        </div>
      </button>
    </div>
  </div>
</template>
