<script setup lang="ts">
import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import { reactive } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import { getBreadcrumbs } from '~/components/BreadCrumbs/helpers'
import HoverProductSwiper from '~/components/Swiper/ProductSwiper/HoverProductSwiper.vue'
import { useCartStore } from '~/stores/cart'
import { storeToRefs } from 'pinia'

const cartStore = useCartStore()
const userStore = useUserStore()
const { showAuthForm, isAuth } = storeToRefs(userStore)

const checkAuthForm = () => {
  if (!isAuth.value) showAuthForm.value = true
  return showAuthForm.value
}

const {
  public: { backendUrl },
} = useRuntimeConfig()

const route = useRoute()
const { slug } = route.params

const { data } = await useAsyncData(`products-list-${slug}`, () =>
  $fetch(`${backendUrl}/api/products/category-slug/${slug.at(-1)}`, {
    query: {
      addition_data: 1,
      with_specs: 1,
    },
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
    },
  })
)

console.log(data.value, 'YESS')

const add = async product => {
  if (!product?.id || product.count === 'Нет в наличии' || checkAuthForm()) return

  try {
    await cartStore.addToCart({
      ...product,
      quantity: 1,
    })
  } catch (error) {
    console.error('Ошибка при добавлении в корзину:', error)
  }
}

const loading = ref(false)

useHead({
  title: `${data.value.category.name} | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты для строительства и ремота, категория ${data.value.category.name}`,
    },
  ],
})

const breadcrumbs = [
  {
    name: 'Категории',
    url: '/category',
  },
  ...getBreadcrumbs(data.value.category.parents.slice(0, -1)),
  {
    name: data.value.category.name,
    url: '/products/category/' + slug.join('/'),
  },
]

const state = reactive({
  priceRange: {
    min: data.value.category.min_price,
    max: data.value.category.max_price,
    currentMin: data.value.category.min_price,
    currentMax: data.value.category.max_price,
    inputMin: data.value.category.min_price,
    inputMax: data.value.category.max_price,
    minTimeout: null,
    maxTimeout: null,
  },
  ui: {
    showFilters: false,
    isGrid: false,
    visibleItems: 8,
    isLoading: false,
    products: data.value.pagination.total,
  },
  filters: {
    selectedBrands: [],
  },
  sort: 'price_asc',
  showSortDropdown: false,
  products: data.value.products || [],
  pagination: data.value.pagination,
})

const sortOptions = [
  { value: 'price_desc', label: 'Сначала дорогие' },
  { value: 'price_asc', label: 'Сначала недорогие' },
]

const changeSort = value => {
  state.sort = value
  state.showSortDropdown = false
  searchData()
}

const searchData = async () => {
  loading.value = true
  const query = {
    addition_data: 0,
  }

  if (state.sort) query.sort = state.sort

  const { currentMin, min, currentMax, max } = state.priceRange
  if (currentMin !== min || currentMax !== max) {
    query.price_min = currentMin
    query.price_max = currentMax
  }

  if (state.filters.selectedBrands.length > 0) {
    query['brands[]'] = state.filters.selectedBrands
  }

  query.page = state.pagination.current_page

  try {
    const data = await $fetch(`${backendUrl}/api/products/category-slug/${slug.at(-1)}`, {
      query,
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    })

    state.products = data.products
    state.pagination = data.pagination
  } catch (e) {
    console.log(e)
  } finally {
    loading.value = false
  }
}

const changePage = async page => {
  state.pagination.current_page = page
  await searchData()
}

const validProducts = computed(() => {
  return state.products.filter(
    item => item.slug && typeof item.slug === 'string' && !item.slug.includes('.') && item.slug.trim() !== ''
  )
})

const getProductLink = item => {
  if (!item.slug || typeof item.slug !== 'string') {
    console.error('Invalid product slug:', item)
    return '/404'
  }
  return `/products/${encodeURIComponent(item.slug)}`
}

const showGrid = () => {
  state.ui.isGrid = true
}

const showList = () => {
  state.ui.isGrid = false
}

const loadMoreItems = () => {
  if (state.ui.isLoading) return
  state.ui.isLoading = true
}

const toggleFilters = () => {
  state.ui.showFilters = !state.ui.showFilters
}

const handleMinPriceInput = value => {
  if (!isNaN(value)) {
    if (value >= state.priceRange.min && value <= state.priceRange.max) {
      state.priceRange.inputMin = value
      state.priceRange.currentMin = value
      state.priceRange.minTimeout = setTimeout(() => {
        state.priceRange.currentMin = Math.min(Math.max(value, state.priceRange.min), state.priceRange.currentMax - 1)
      }, 500)
    } else {
      state.priceRange.inputMin = state.priceRange.min
    }
  }
}

const handleMaxPriceInput = value => {
  if (!isNaN(value)) {
    if (value >= state.priceRange.min && value <= state.priceRange.max) {
      state.priceRange.inputMax = value

      if (state.priceRange.maxTimeout) clearTimeout(state.priceRange.maxTimeout)

      state.priceRange.maxTimeout = setTimeout(() => {
        state.priceRange.currentMax = Math.max(Math.min(value, state.priceRange.max), state.priceRange.currentMin + 1)
      }, 500)
    } else {
      state.priceRange.inputMax = state.priceRange.max
    }
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

const checkScreenSize = () => {
  if (process.client) {
    // Переключаем на grid view при ширине экрана >= lg (1024px в Tailwind)
    state.ui.isGrid = window.innerWidth <= 1024
  }
}

// Устанавливаем обработчик при монтировании компонента
onMounted(() => {
  checkScreenSize() // Проверяем при загрузке
  window.addEventListener('resize', checkScreenSize)
})

// Удаляем обработчик при размонтировании компонента
onBeforeUnmount(() => {
  if (process.client) {
    window.removeEventListener('resize', checkScreenSize)
  }
})

</script>

<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />
    <CategoryDescription :data="data.category" :products-count="state.products.length" />

    <!-- Заголовок результатов -->
    <div
      class="flex flex-col md:flex-row items-start md:items-center justify-between bg-white rounded-lg shadow-sm p-4 mb-6"
    >
      <div class="flex items-center w-full md:w-auto">
        <button
          @click="toggleFilters"
          class="lg:hidden flex items-center space-x-1 ml-4 text-red-600 hover:text-red-800 transition-colors"
        >
          <Icon name="material-symbols:filter-alt" class="h-5 w-5" />
          <span class="font-medium">Фильтры</span>
        </button>
      </div>

      <div class="flex items-center space-x-4 mt-3 md:mt-0">
        <div class="relative">
          <button
            @click.stop="state.showSortDropdown = !state.showSortDropdown"
            class="flex items-center gap-1 text-gray-700 hover:text-red-600 transition-colors"
          >
            <span>{{ sortOptions.find(opt => opt.value === state.sort)?.label }}</span>
            <Icon
              name="material-symbols:keyboard-arrow-down-rounded"
              class="w-5 h-5 transition-transform duration-200"
              :class="{ 'transform rotate-180': state.showSortDropdown }"
            />
          </button>

          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <div v-if="state.showSortDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
              <div class="py-1">
                <button
                  v-for="option in sortOptions"
                  :key="option.value"
                  @click="changeSort(option.value)"
                  class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 transition-colors"
                  :class="{ 'bg-gray-100 text-red-600': option.value === state.sort }"
                >
                  {{ option.label }}
                </button>
              </div>
            </div>
          </transition>
        </div>

<div class="hidden lg:flex items-center space-x-1">
  <button
    @click="state.ui.isGrid = true"
    :class="{ 'bg-gray-100 text-red-600': state.ui.isGrid }"
    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
    :disabled="state.ui.isGrid"
  >
    <Icon name="material-symbols:grid-view-rounded" class="w-5 h-5" />
  </button>
  <button
    @click="state.ui.isGrid = false"
    :class="{ 'bg-gray-100 text-red-600': !state.ui.isGrid }"
    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
    :disabled="!state.ui.isGrid"
  >
    <Icon name="material-symbols:view-stream-rounded" class="w-5 h-5" />
  </button>
</div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="flex flex-col lg:flex-row gap-6">
      <!-- Боковая панель фильтров - скрыта на мобильных и планшетах -->
      <div class="hidden lg:block w-full lg:w-1/4">
        <div class="bg-white rounded-lg shadow-sm p-5 sticky top-4">
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
                  type="number"
                  :min="state.priceRange.min"
                  :max="state.priceRange.max"
                  :modelValue="state.priceRange.inputMin"
                  @update:modelValue="handleMinPriceInput"
                  @keyup.enter="updateMinPriceFromInput"
                  class="w-full"
                />
              </div>
              <span class="text-gray-400">—</span>
              <div class="relative flex-1">
                <TextInput
                  type="number"
                  :min="state.priceRange.min"
                  :max="state.priceRange.max"
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
                  class="absolute h-1 bg-red-500 rounded-full top-2"
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
              <div class="flex gap-2" v-for="brand in data.category.brands" @click="toggleBrand(brand)" :key="brand">
                <input
                  type="checkbox"
                  :checked="state.filters.selectedBrands.includes(brand)"
                  class="rounded text-red-600 focus:ring-red-500 border-gray-300"
                />
                <span class="text-gray-700">{{ brand }}</span>
              </div>
            </div>
          </div>

          <!-- Кнопки фильтров -->
          <div class="space-y-3">
            <button
              @click="searchData"
              class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 px-4 transition-colors font-medium"
            >
              Показать товары
            </button>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-3/4">
        <div
          v-if="state.products.length > 0"
          :class="state.ui.isGrid ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-6'"
        >
          <NuxtLink
            v-for="item in validProducts"
            :to="getProductLink(item)"
            :key="item.id"
            :class="[
              'bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300',
              state.ui.isGrid ? 'flex flex-col h-full' : 'flex flex-col md:flex-row',
            ]"
          >
          <div
            :class="[
              'relative overflow-hidden',
              state.ui.isGrid ? 'aspect-square h-auto w-full' : 'h-48 w-full md:w-40 lg:w-48 flex-shrink-0',
            ]"
          >
            <HoverProductSwiper 
              :slides="item.images" 
              :mode="state.ui.isGrid ? 'grid' : 'list'"
            />
              <div
                v-if="item.discount"
                class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded"
              >
                -{{ item.discount }}%
              </div>
            </div>

            <div class="flex flex-col p-4 flex-grow">
              <div v-if="state.ui.isGrid">
                <h3 class="font-medium text-base mb-2 line-clamp-2">
                  {{ item.name }}
                </h3>
                <span class="text-gray-500 text-xs mb-1">Код: {{ item.article }}</span>

                <div class="my-3">
                  <span class="text-red-600 font-bold text-xl">{{ item.price.toLocaleString() }} ₽</span>
                </div>

                <div class="mt-auto">
                  <p
                    class="text-sm mb-4 flex items-center"
                    :class="item.count === 'Нет в наличии' ? 'text-red-600' : 'text-green-600'"
                  >
                    <Icon
                      :name="
                        item.count === 'Нет в наличии'
                          ? 'material-symbols:close-rounded'
                          : 'material-symbols:check-rounded'
                      "
                      class="h-5 w-5 mr-2"
                    />
                    {{ item.count }}
                  </p>

                  <button
                    :disabled="item.count === 'Нет в наличии' || cartStore.checkProductInCart(item)"
                    @click.prevent="add(item)"
                    class="w-full text-white py-2.5 px-4 rounded-lg transition-colors font-medium text-sm"
                    :class="
                      item.count === 'Нет в наличии' || cartStore.checkProductInCart(item)
                        ? 'bg-gray cursor-not-allowed'
                        : 'bg-red-600 hover:bg-red-700'
                    "
                  >
                    {{ cartStore.checkProductInCart(item) ? 'Добавлено' : 'В корзину' }}
                  </button>
                </div>
              </div>

              <div v-else>
                <div class="flex flex-col md:flex-row gap-4">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-base md:mb-2">
                      {{ item.name }}
                    </h3>
                    <span class="text-sm block mb-2">Код: {{ item.article }}</span>

                    <div class="text-sm space-y-3 mt-2 w-[50%]">
                      <div v-if="item?.specificationsB?.length">
                        <div v-for="spec in item.specificationsB" :key="spec.id" class="flex items-baseline min-w-0">
                          <span class="text-sm truncate">{{ spec.name }}</span>
                          <span class="flex-1 border-b border-dotted border-gray-300 mx-2"></span>
                          <span class="font-medium text-sm whitespace-nowrap">{{ spec.value }}</span>
                        </div>
                      </div>
                      <div v-else>
                        <div class="text-sm italic">Характеристики не указаны</div>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-col items-start">
                    <div class="mb-4">
                      <span class="text-red-600 font-bold text-xl">{{ item.price.toLocaleString() }} ₽</span>
                    </div>

                    <p
                      class="text-sm mb-4 flex items-center"
                      :class="item.count === 'Нет в наличии' ? 'text-red-600' : 'text-green-600'"
                    >
                      <Icon
                        :name="
                          item.count === 'Нет в наличии'
                            ? 'material-symbols:close-rounded'
                            : 'material-symbols:check-rounded'
                        "
                        class="h-5 w-5 mr-2"
                      />
                      {{ item.count }}
                    </p>

                    <button
                      :disabled="item.count === 'Нет в наличии' || cartStore.checkProductInCart(item)"
                      @click.prevent="add(item)"
                      class="w-full text-white py-2.5 px-4 rounded-lg transition-colors font-medium text-sm"
                      :class="
                        item.count === 'Нет в наличии' || cartStore.checkProductInCart(item)
                          ? 'bg-gray cursor-not-allowed'
                          : 'bg-red-600 hover:bg-red-700'
                      "
                    >
                      {{ cartStore.checkProductInCart(item) ? 'Добавлено' : 'В корзину' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </NuxtLink>
        </div>

        <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
          <Icon name="ion:search-outline" class="mx-auto block text-gray-400 h-20 w-20" />
          <h3 class="mt-4 text-lg font-semibold text-gray-900">Товары не найдены</h3>
          <p class="mt-1 text-gray-500">Попробуйте изменить параметры фильтрации</p>
          <button
            @click="resetPrice"
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Сбросить фильтры
          </button>
        </div>

        <div
          class="flex justify-center mt-8"
          v-if="state.ui.visibleItems < state.products.length && state.products.length > 0"
        >
          <button
            @click="loadMoreItems"
            :disabled="state.ui.isLoading"
            class="bg-white border border-red-600 text-red-600 hover:bg-red-50 py-2.5 px-8 rounded-lg transition-colors font-medium flex items-center space-x-2"
          >
            <span v-if="!state.ui.isLoading">Показать ещё</span>
            <Icon v-if="state.ui.isLoading" name="svg-spinners:ring-resize" class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>

    <div class="w-full p-4 flex justify-center items-center gap-4">
      <button
        @click="changePage(1)"
        v-if="state.pagination.current_page !== 1 && !loading"
        class="w-14 h-14 flex items-center justify-center text-xl font-bold rounded-full bg-gray hover:bg-primary-hover text-white"
      >
        1
      </button>
      <div v-if="state.pagination.current_page > 2 && !loading">...</div>
      <button
        @click="changePage(state.pagination.current_page - 1)"
        class="w-14 h-14 flex items-center justify-center text-xl font-bold rounded-full bg-gray hover:bg-primary-hover text-white"
        v-if="state.pagination.current_page !== 1 && state.pagination.current_page - 1 !== 1 && !loading"
      >
        {{ state.pagination.current_page - 1 }}
      </button>
      <button
        class="w-14 h-14 flex items-center justify-center text-xl font-bold rounded-full bg-primary hover:bg-primary-hover text-white"
      >
        {{ state.pagination.current_page }}
      </button>
      <button
        @click="changePage(state.pagination.current_page + 1)"
        class="w-14 h-14 flex items-center justify-center text-xl font-bold rounded-full bg-gray hover:bg-primary-hover text-white"
        v-if="state.pagination.has_more && state.pagination.current_page + 1 !== state.pagination.last_page && !loading"
      >
        {{ state.pagination.current_page + 1 }}
      </button>
      <div
        v-if="
          state.pagination.current_page !== state.pagination.last_page &&
          state.pagination.current_page !== state.pagination.last_page - 1 &&
          !loading
        "
      >
        ...
      </div>
      <button
        @click="changePage(state.pagination.last_page)"
        v-if="state.pagination.current_page !== state.pagination.last_page && !loading"
        class="w-14 h-14 flex items-center justify-center text-xl font-bold rounded-full bg-gray hover:bg-primary-hover text-white"
      >
        {{ state.pagination.last_page || '>' }}
      </button>
    </div>

    <!-- Модальное окно фильтров для мобильных -->
    <transition name="fade">
      <div v-if="state.ui.showFilters" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="toggleFilters"></div>
    </transition>

    <transition name="slide">
      <div v-if="state.ui.showFilters" class="fixed inset-y-0 left-0 w-80 bg-white z-50 p-6 overflow-y-auto shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-900">Все фильтры</h2>
          <button @click="toggleFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
            <Icon name="material-symbols:close-rounded" class="h-6 w-6" />
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
                  class="absolute h-1 bg-red-500 rounded-full top-2"
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

          <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Производители</h3>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
              <div class="flex gap-2" v-for="brand in data.category.brands" @click="toggleBrand(brand)" :key="brand">
                <input
                  type="checkbox"
                  :checked="state.filters.selectedBrands.includes(brand)"
                  class="rounded text-red-600 focus:ring-red-500 border-gray-300"
                />
                <span class="text-gray-700">{{ brand }}</span>
              </div>
            </div>
          </div>
          <button
            @click="
              () => {
                toggleFilters()
                searchData()
              }
            "
            class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-3 px-4 transition-colors duration-200 font-medium shadow-md hover:shadow-lg active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          >
            Показать товары
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
