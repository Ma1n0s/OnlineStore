<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Header from '../Header.vue';
import Footer from '../Footer.vue';

const currentPrice = ref(23053);

const min = ref(0);
const max = ref(1000);
const minPrice = ref(0); // Текущее минимальное значение
const maxPrice = ref(1000); // Текущее максимальное значение
const minPriceInput = ref(0); // Временное значение для минимальной цены
const maxPriceInput = ref(1000); // Временное значение для максимальной цены
let minPriceTimeout = null;
let maxPriceTimeout = null;

const showFilters = ref(false);
const isGrid = ref(true);
const items = ref([
  { id: 1, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg', price: 13290 },
  { id: 2, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg', price: 14290 },

]);

const visibleItems = ref(10); 
const isLoading = ref(false); 

const loadMoreItems = () => {
  if (isLoading.value) return; 
  isLoading.value = true;

  setTimeout(() => {
    const newItems = [
      { id: items.value.length + 1, code: '15640682', title: 'Новый элемент', image: '/path/to/image.jpg', price: 15290 },
      { id: items.value.length + 2, code: '15640682', title: 'Новый элемент', image: '/path/to/image.jpg', price: 16290 },
    ];
    items.value = [...items.value, ...newItems];
    visibleItems.value += 2;
    isLoading.value = false;
  }, 1000);
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
}

const showGrid = () => {
  isGrid.value = true;
};

const showList = () => {
  isGrid.value = false;
};

const updateMinPrice = (event) => {
  minPrice.value = Math.min(Number(event.target.value), maxPrice.value - 1);
  minPriceInput.value = minPrice.value; // Синхронизируем значение
};

const updateMaxPrice = (event) => {
  maxPrice.value = Math.max(Number(event.target.value), minPrice.value + 1);
  maxPriceInput.value = maxPrice.value; // Синхронизируем значение
};

const handleMinPriceInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    minPriceInput.value = value;

    // Сброс таймера
    if (minPriceTimeout) clearTimeout(minPriceTimeout);

    minPriceTimeout = setTimeout(() => {
      minPrice.value = Math.min(Math.max(value, min.value), maxPrice.value - 1);
    }, 5000); 
  }
};

const handleMaxPriceInput = (event) => {
  const value = Number(event.target.value);
  if (!isNaN(value)) {
    maxPriceInput.value = value;

    // Сброс предыдущего таймера
    if (maxPriceTimeout) clearTimeout(maxPriceTimeout);

    maxPriceTimeout = setTimeout(() => {
      maxPrice.value = Math.max(Math.min(value, max.value), minPrice.value + 1);
    }, 5000); 
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
  if (type === 'min') {
    minPriceInput.value = minPrice.value; 
  } else if (type === 'max') {
    maxPriceInput.value = maxPrice.value; 
  }
};

const resetPrice = () => {
  minPrice.value = min.value;
  maxPrice.value = max.value;
  minPriceInput.value = min.value;
  maxPriceInput.value = max.value;
};

const filteredItems = () => {
  return items.value.filter(
    (item) => item.price >= minPrice.value && item.price <= maxPrice.value
  );
};
</script>

<template>
    <Header/>
    <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
        <nav class="flex space-x-2 text-gray-600 mb-4">
            <RouterLink to="/" class="hover:underline">Главная</RouterLink>
            <p>/</p>
            <RouterLink to="/category/troitelnyj-instrument" class="font-semibold">Инструменты</RouterLink>
            <p>/</p>
            <RouterLink to="" class="font-semibold">Шуруповерты</RouterLink>
        </nav>
        <div class="flex items-center space-x-2">
            <h2 class="font-bold text-2xl">Шуруповерты</h2>
            <p>4945 товаров</p>
        </div>

        <!-- Сетка из 4 колонок -->
        <div class="flex gap-4 mt-4"> 
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 w-52 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-52 h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 w-52 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-52 h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 w-52 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-52 h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 w-52 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-52 h-32 object-cover rounded-md" />
            </div>
        </div>

        <div class="flex flex-col md:flex-row p-4 mt-4 items-center">
            <p class="flex-1 font-bold mr-2">Найдено 5182 товара</p>
            <div class="flex space-x-2 items-center">
            <p>Сортировать по:</p>
            <span class="px-4 py-2 cursor-pointer">По популярности</span>
            <span class="px-4 py-2 cursor-pointer hover:text-gray-800">Отзывам</span>
            <span class="px-4 py-2 cursor-pointer hover:text-gray-800">Рейтингу</span>
            <span class="px-4 py-2 cursor-pointer hover:text-gray-800">Цене</span>
            </div>
            <div class="flex justify-end ">
                <button @click="showGrid" class="mr-2">
                    <img src="/public/interface-design-structure-outline.svg" alt="" class="w-5 h-5" />
                </button>
                <button @click="showList">
                    <img src="/public/four-squares-button-of-view-options.svg" alt="" class="w-5 h-5 mr-2 filter text-red-500" />
                </button>
            </div>
        </div>

        <div class="flex mt-4">
            <div class="w-1/4 pr-4">
                 <hr>
                <div class="mb-4">
                    <div class="flex items-center">
                        <img src="/public/arrow-left.svg" alt="стрелка меньше" class="w-3 h-3 mr-3"/>
                        <span class="text-gray-600">Инструмент</span>
                    </div>
                    <div class="mt-2 p-2 bg-gray-100 rounded">
                        <span class="text-black">Шуруповерты</span>
                    </div>
                </div>
                <hr>

                <div class="mb-4">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Цена, ₽</span>
                        <button @click="resetPrice" class="h-10 px-4 py-2 flex items-center">Сбросить</button>
                    </div>
                    <div class="flex justify-between items-center">
                        <input
                        type="text"
                        :value="minPriceInput"
                        @input="handleMinPriceInput"
                        @keyup.enter="updateMinPriceFromInput"
                        class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2"
                        />
                        <input
                        type="text"
                        :value="maxPriceInput"
                        @input="handleMaxPriceInput"
                        @keyup.enter="updateMaxPriceFromInput"
                        class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2"
                        />
                    </div>
                    <div class="p-4">
                        <div>
                        <div class="relative h-8">
                            <div class="absolute w-full h-2 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
                            <div
                            class="absolute h-2 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
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
                </div>
                <div class="mb-4">
                    <h2 class="text-xl font-bold mb-4">Производители</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Aurora
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Champion
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                DDE
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                EuroPower
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Gigant
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Inforce
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Patriot
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Спец
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                CARVER
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                DAEWOO
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Elitech
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                FUBAG
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                Huter
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                LIFAN
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                ЗУБР
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                TCC
                            </label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="h-10 px-4 py-2 flex items-center">Показать еще 68</button>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="flex items-center mb-2">
                            <div class="bg-white border rounded-2xl p-2 flex items-center h-10 hover:text-gray-700">
                                <button @click="toggleFilters" class="text-sm">Все фильтры</button>
                                <img src="/public/filter.svg" alt="Фильтр" class="ml-2 w-4 h-4 shadow-md" />
                            </div>
                        </div>
                        <button class="text-white bg-red-600 rounded-2xl p-2 text-sm h-10 hover:bg-red-700">Показать 5 185 товаров</button>
                    </div>
                    <div>
                        <transition name="fade">
                        <div 
                            v-if="showFilters" 
                            class="fixed inset-0 bg-black bg-opacity-50 z-40" 
                            @click="toggleFilters"
                        ></div>
                        </transition>

                        <transition name="slide">
                        <div 
                            v-if="showFilters" 
                            class="fixed inset-y-0 left-0 w-64 bg-white z-50 p-4 overflow-y-auto shadow-lg"
                        >
                            <button 
                            @click="toggleFilters" 
                            class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl"
                            >
                            &times;
                            </button>

                            <h2 class="font-bold mb-4">Все фильтры</h2>
                            <div>
                            <h3 class="font-semibold mb-2">Фильтр</h3>
                            <ul class="pl-4">
                                <li>123</li>
                                <li>123</li>
                            </ul>
                            </div>
                        </div>
                        </transition>
                    </div>
                </div>
            </div>

            <div class="w-3/4">
                <div :class="isGrid ? 'grid grid-cols-4 gap-4' : 'flex flex-col space-y-4'">
                    <div
                        class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden transition-shadow duration-300 hover:shadow-lg"
                        :class="isGrid ? 'w-full' : 'w-full'"
                        v-for="(item, index) in items.slice(0, visibleItems)"
                        :key="item.id"
                    >
                        <div class="p-4">
                            <div class="flex items-center">
                                <p>код: {{ item.code }}</p>
                                <img src="/public/stars.svg" alt="favorite" class="ml-2 w-4 h-4 hover:text-rose-700" />
                            </div>
                            <img :src="item.image" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover">
                            <RouterLink to="/product/productCard"><h3 class="mt-2 text-xl font-semibold hover:text-rose-600">{{ item.title }}</h3></RouterLink>
                            <p class="text-green-500 mb-2">В наличии > 100 шт.</p>
                            <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
                            <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
                            <span class="flex text-red-500">13 290 ₽</span>
                            <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-4" v-if="visibleItems < items.length">
                    <button @click="loadMoreItems" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                        Показать ещё
                    </button>
                </div>
            </div>
        </div>
        
    </div>
    <Footer/>
</template>

<!-- <style>
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 16px;
  height: 16px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
}
</style> -->


<style scoped>
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 16px;
  height: 16px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
  width: 16px;
  height: 16px;
  background: #3b82f6;
  border-radius: 50%;
  cursor: pointer;
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
@apply bg-transparent h-2 rounded-full;
}
  
input[type="range"]::-webkit-slider-thumb {
@apply bg-red-500 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
transform: translateY(-50%);
position: relative;
z-index: 10; 
top: 50%; 
margin-top: 12px; 
}
  
input[type="range"]::-moz-range-track {
@apply bg-transparent h-2 rounded-full;
}
  
input[type="range"]::-moz-range-thumb {
@apply bg-red-500 w-5 h-5 rounded-full appearance-none cursor-pointer pointer-events-auto;
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
.fade-enter-active, .fade-leave-active {
transition: opacity 0.3s;
}
.fade-enter, .fade-leave-to {
opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter, .slide-leave-to {
  transform: translateX(-100%);
}
</style>