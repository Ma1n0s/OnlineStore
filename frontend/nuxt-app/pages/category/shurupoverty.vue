<script setup>
import { ref } from "vue";
import BrandSwiper from "~/components/Swiper/BrandSwiper.vue";

const min = ref(0);
const max = ref(30000);
const minPrice = ref(0);
const maxPrice = ref(30000);
const minPriceInput = ref(0);
const maxPriceInput = ref(30000);
let minPriceTimeout = null;
let maxPriceTimeout = null;


const showFilters = ref(false);
const isGrid = ref(true);
const visibleItems = ref(8);
const isLoading = ref(false);

const selectedBrands = ref([]);

const items = ref([
  {
    id: 1,
    code: "15640682",
    title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348",
    image: "https://via.placeholder.com/300x300?text=Ryobi",
    price: 13290,
    oldPrice: 15990,
    discount: 17,
    stock: 100,
    brand: "Ryobi"
  },
  {
    id: 2,
    code: "15640683",
    title: "Дрель-шуруповерт Bosch GSR 12V-15 06019A8021",
    image: "https://via.placeholder.com/300x300?text=Bosch",
    price: 14290,
    oldPrice: 16990,
    discount: 16,
    stock: 50,
    brand: "Bosch"
  },
  {
    id: 3,
    code: "15640684",
    title: "Шуруповерт Makita DF457DWE 165024-8",
    image: "https://via.placeholder.com/300x300?text=Makita",
    price: 15290,
    oldPrice: 17990,
    discount: 15,
    stock: 30,
    brand: "Makita"
  },
  {
    id: 4,
    code: "15640685",
    title: "Шуруповерт DeWalt DCD771C2",
    image: "https://via.placeholder.com/300x300?text=DeWalt",
    price: 18990,
    oldPrice: 21990,
    discount: 14,
    stock: 25,
    brand: "DeWalt"
  },
  {
    id: 5,
    code: "15640686",
    title: "Шуруповерт Metabo BS 18 LTX 600129700",
    image: "https://via.placeholder.com/300x300?text=Metabo",
    price: 16290,
    oldPrice: 18990,
    discount: 14,
    stock: 20,
    brand: "Metabo"
  },
  {
    id: 6,
    code: "15640687",
    title: "Шуруповерт Hitachi DS18DSAL",
    image: "https://via.placeholder.com/300x300?text=Hitachi",
    price: 13990,
    oldPrice: 16990,
    discount: 18,
    stock: 15,
    brand: "Hitachi"
  },
  {
    id: 7,
    code: "15640688",
    title: "Шуруповерт AEG BSB 12C2-120X",
    image: "https://via.placeholder.com/300x300?text=AEG",
    price: 14990,
    oldPrice: 17990,
    discount: 17,
    stock: 10,
    brand: "AEG"
  },
  {
    id: 8,
    code: "15640689",
    title: "Шуруповерт Black+Decker BDCDD12K",
    image: "https://via.placeholder.com/300x300?text=BlackDecker",
    price: 8990,
    oldPrice: 11990,
    discount: 25,
    stock: 40,
    brand: "Black+Decker"
  }
]);


const loadMoreItems = () => {
  if (isLoading.value) return;
  isLoading.value = true;

  setTimeout(() => {
    const newItems = [
      {
        id: items.value.length + 1,
        code: "15640690",
        title: "Шуруповерт Hilti SF 6H-A22",
        image: "https://via.placeholder.com/300x300?text=Hilti",
        price: 24990,
        oldPrice: 28990,
        discount: 14,
        stock: 5,
        brand: "Hilti"
      },
      {
        id: items.value.length + 2,
        code: "15640691",
        title: "Шуруповерт Milwaukee M18 BPS-0",
        image: "https://via.placeholder.com/300x300?text=Milwaukee",
        price: 21990,
        oldPrice: 25990,
        discount: 15,
        stock: 8,
        brand: "Milwaukee"
      }
    ];
    items.value = [...items.value, ...newItems];
    visibleItems.value += 2;
    isLoading.value = false;
  }, 1000);
};

const toggleFilters = () => {
  showFilters.value = !showFilters.value;
};

const showGrid = () => {
  isGrid.value = true;
};

const showList = () => {
  isGrid.value = false;
};

const updateMinPrice = (event) => {
  minPrice.value = Math.min(Number(event.target.value), maxPrice.value - 1);
  minPriceInput.value = minPrice.value;
};

const updateMaxPrice = (event) => {
  maxPrice.value = Math.max(Number(event.target.value), minPrice.value + 1);
  maxPriceInput.value = maxPrice.value;
};

const handleMinPriceInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    minPriceInput.value = value;

    if (minPriceTimeout) clearTimeout(minPriceTimeout);

    minPriceTimeout = setTimeout(() => {
      minPrice.value = Math.min(Math.max(value, min.value), maxPrice.value - 1);
    }, 500);
  }
};

const handleMaxPriceInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    maxPriceInput.value = value;

    if (maxPriceTimeout) clearTimeout(maxPriceTimeout);

    maxPriceTimeout = setTimeout(() => {
      maxPrice.value = Math.max(Math.min(value, max.value), minPrice.value + 1);
    }, 500);
  }
};

const updateMinPriceFromInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    minPrice.value = Math.min(Math.max(value, min.value), maxPrice.value - 1);
    minPriceInput.value = value;
  }
};

const updateMaxPriceFromInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    maxPrice.value = Math.max(Math.min(value, max.value), minPrice.value + 1);
    maxPriceInput.value = value;
  }
};

const handleSliderChange = (type) => {
  if (type === "min") {
    minPriceInput.value = minPrice.value;
  } else if (type === "max") {
    maxPriceInput.value = maxPrice.value;
  }
};

const resetPrice = () => {
  minPrice.value = min.value;
  maxPrice.value = max.value;
  minPriceInput.value = min.value;
  maxPriceInput.value = max.value;
  selectedBrands.value = [];
};

const filteredItems = () => {
  return items.value.filter((item) => {
    const priceMatch = item.price >= minPrice.value && item.price <= maxPrice.value;
    const brandMatch = selectedBrands.value.length === 0 || selectedBrands.value.includes(item.brand);
    return priceMatch && brandMatch;
  });
};

const toggleBrand = (brand) => {
  if (selectedBrands.value.includes(brand)) {
    selectedBrands.value = selectedBrands.value.filter(b => b !== brand);
  } else {
    selectedBrands.value = [...selectedBrands.value, brand];
  }
};
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-6">
    <nav class="flex items-center text-sm text-gray-600 mb-6">
      <NuxtLink to="/" class="hover:text-red-600 transition-colors">Главная</NuxtLink>
      <span class="mx-2 text-gray-400">/</span>
      <NuxtLink to="/category/troitelnyj-instrument" class="hover:text-red-600 transition-colors">Инструменты</NuxtLink>
      <span class="mx-2 text-gray-400">/</span>
      <NuxtLink to="" class="text-red-600 font-medium">Шуруповерты</NuxtLink>
    </nav>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
      <div v-for="i in 4" :key="i" class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden group">
        <div class="p-4">
          <div class="font-bold text-gray-900 group-hover:text-red-600 transition-colors">Шуруповерты</div>
          <p class="text-gray-500 text-sm mt-1">4 754 товара</p>
          <img src="https://via.placeholder.com/300x300?text=Drill" alt="Шуруповерты" class="w-full h-32 object-contain mt-3" />
        </div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between bg-white rounded-lg shadow-sm p-4 mb-6">
      <p class="text-gray-700 mb-3 md:mb-0">Найдено <span class="font-semibold">{{ filteredItems().length }} товара</span></p>
      
      <div class="flex items-center space-x-4">
        <div class="flex items-center">
          <span class="text-gray-700 mr-2">Сортировать по:</span>
          <select class="bg-gray-50 border border-gray-300 text-gray-700 rounded-lg px-3 py-1 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
            <option>По популярности</option>
            <option>Отзывам</option>
            <option>Рейтингу</option>
            <option>Цене</option>
          </select>
        </div>
        
        <div class="flex items-center space-x-1">
          <button @click="showGrid" :class="{'bg-gray-100 text-red-600': isGrid}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </button>
          <button @click="showList" :class="{'bg-gray-100 text-red-600': !isGrid}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
      <div class="w-full lg:w-1/4">
        <div class="bg-white rounded-lg shadow-sm p-5 sticky top-4">
          <div class="mb-6">
            <div class="flex items-center text-gray-700 mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              <span>Инструмент</span>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <span class="font-medium">Шуруповерты</span>
            </div>
          </div>

          <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
              <h3 class="font-semibold text-gray-900">Цена, ₽</h3>
              <button @click="resetPrice" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">Сбросить</button>
            </div>
            
            <div class="flex items-center space-x-3 mb-4">
              <div class="relative flex-1">
                <input
                  type="text"
                  :value="minPriceInput"
                  @input="handleMinPriceInput"
                  @keyup.enter="updateMinPriceFromInput"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                />
              </div>
              <span class="text-gray-400">—</span>
              <div class="relative flex-1">
                <input
                  type="text"
                  :value="maxPriceInput"
                  @input="handleMaxPriceInput"
                  @keyup.enter="updateMaxPriceFromInput"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                />
              </div>
            </div>
            
            <div class="px-2">
              <div class="relative h-8">
                <div class="absolute w-full h-1 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
                <div
                  class="absolute h-1 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
                  :style="{
                    left: `${((minPrice - min) / (max - min)) * 100}%`,
                    width: `${((maxPrice - minPrice) / (max - min)) * 100}%`,
                  }"
                ></div>
                <input
                  type="range"
                  :min="min"
                  :max="max"
                  v-model.number="minPrice"
                  @input="handleSliderChange('min')"
                  class="absolute w-full appearance-none pointer-events-none"
                />
                <input
                  type="range"
                  :min="min"
                  :max="max"
                  v-model.number="maxPrice"
                  @input="handleSliderChange('max')"
                  class="absolute w-full appearance-none pointer-events-none"
                />
              </div>
            </div>
          </div>

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
                  :checked="selectedBrands.includes(brand)"
                  class="rounded text-red-600 focus:ring-red-500 border-gray-300"
                  @change="toggleBrand(brand)"
                >
                <span class="text-gray-700">{{ brand }}</span>
              </label>
            </div>
          </div>

          <div class="space-y-3">
            <button @click="toggleFilters" class="w-full flex items-center justify-center space-x-2 border border-gray-300 rounded-xl py-2 px-4 hover:bg-gray-50 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              <span>Все фильтры</span>
            </button>
            <button class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-2 px-4 transition-colors font-medium">
              Показать {{ filteredItems().length }} товаров
            </button>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-3/4">
        <div v-if="filteredItems().length > 0" :class="isGrid ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5' : 'space-y-5'">
          <div
            v-for="item in filteredItems().slice(0, visibleItems)"
            :key="item.id"
            :class="isGrid ? 'bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden' : 'bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 overflow-hidden flex'"
          >
            <div :class="isGrid ? 'relative' : 'w-1/3'">

              <BrandSwiper />
              <button class="absolute top-3 right-3 p-1 bg-white rounded-full shadow-md hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
              <div v-if="item.discount" class="absolute top-3 left-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                -{{ item.discount }}%
              </div>
            </div>

            <div :class="isGrid ? 'p-4' : 'w-2/3 p-4'">
              <div class="flex justify-between items-start mb-1">
                <span class="text-gray-500 text-xs">Код: {{ item.code }}</span>
              </div>
              
              <NuxtLink to="/product/productCard" class="block">
                <h3 class="font-medium text-gray-900 hover:text-red-600 transition-colors line-clamp-2 mb-2">{{ item.title }}</h3>
              </NuxtLink>
              
              <p class="text-green-600 text-sm mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                В наличии > {{ item.stock }} шт.
              </p>
              
              <div class="mb-3">
                <span class="text-gray-400 line-through text-sm mr-2">{{ item.oldPrice.toLocaleString() }} ₽</span>
                <span class="text-red-600 font-bold text-lg">{{ item.price.toLocaleString() }} ₽</span>
              </div>
              
              <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors font-medium">
                В корзину
              </button>
            </div>
          </div>
        </div>

        <div v-else class="bg-white rounded-lg shadow-sm p-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">Товары не найдены</h3>
          <p class="mt-1 text-gray-500">Попробуйте изменить параметры фильтрации</p>
          <button 
            @click="resetPrice"
            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none"
          >
            Сбросить фильтры
          </button>
        </div>

        <div class="flex justify-center mt-8" v-if="visibleItems < filteredItems().length && filteredItems().length > 0">
          <button 
            @click="loadMoreItems" 
            :disabled="isLoading"
            class="bg-white border border-red-600 text-red-600 hover:bg-red-50 py-2 px-6 rounded-lg transition-colors font-medium flex items-center"
          >
            <span v-if="!isLoading">Показать ещё</span>
            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <transition name="fade">
      <div v-if="showFilters" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="toggleFilters"></div>
    </transition>

    <transition name="slide">
      <div v-if="showFilters" class="fixed inset-y-0 left-0 w-80 bg-white z-50 p-6 overflow-y-auto shadow-xl">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-900">Все фильтры</h2>
          <button @click="toggleFilters" class="text-gray-500 hover:text-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="space-y-6">
          <div>
            <h3 class="font-semibold text-gray-900 mb-3">Цена, ₽</h3>
            <div class="flex items-center space-x-3 mb-4">
              <input
                type="text"
                :value="minPriceInput"
                @input="handleMinPriceInput"
                @keyup.enter="updateMinPriceFromInput"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
              />
              <span class="text-gray-400">—</span>
              <input
                type="text"
                :value="maxPriceInput"
                @input="handleMaxPriceInput"
                @keyup.enter="updateMaxPriceFromInput"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
              />
            </div>
            <div class="px-2">
              <div class="relative h-8">
                <div class="absolute w-full h-1 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
                <div
                  class="absolute h-1 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
                  :style="{
                    left: `${((minPrice - min) / (max - min)) * 100}%`,
                    width: `${((maxPrice - minPrice) / (max - min)) * 100}%`,
                  }"
                ></div>
                <input
                  type="range"
                  :min="min"
                  :max="max"
                  v-model.number="minPrice"
                  @input="handleSliderChange('min')"
                  class="absolute w-full appearance-none pointer-events-none"
                />
                <input
                  type="range"
                  :min="min"
                  :max="max"
                  v-model.number="maxPrice"
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
                  :checked="selectedBrands.includes(brand)"
                  class="rounded text-red-600 focus:ring-red-500 border-gray-300"
                  @change="toggleBrand(brand)"
                >
                <span class="text-gray-700">{{ brand }}</span>
              </label>
            </div>
          </div>
        </div>
        
        <div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-md">
          <button 
            @click="toggleFilters" 
            class="w-full bg-red-600 hover:bg-red-700 text-white rounded-xl py-3 px-4 transition-colors font-medium"
          >
            Показать {{ filteredItems().length }} товаров
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
input[type="range"]::-webkit-slider-thumb {
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

input[type="range"]::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background: #dc2626;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input[type="range"] {
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

input[type="range"]::-webkit-slider-runnable-track {
  @apply bg-transparent h-1 rounded-full;
}

input[type="range"]::-webkit-slider-thumb {
  @apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
  transform: translateY(-50%);
  position: relative;
  z-index: 10;
  top: 50%;
  margin-top: 8px;
}

input[type="range"]::-moz-range-track {
  @apply bg-transparent h-1 rounded-full;
}

input[type="range"]::-moz-range-thumb {
  @apply bg-red-600 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
  position: relative;
  z-index: 10;
  top: 50%;
  margin-top: 2px;
}

input[type="range"]:first-of-type::-webkit-slider-thumb {
  z-index: 20;
}

input[type="range"]:last-of-type::-webkit-slider-thumb {
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