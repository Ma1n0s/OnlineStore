<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Header from '../Header.vue';
import Footer from '../Footer.vue';


const currentPrice = ref(23053);

const min = ref(0);
const max = ref(1000);
const minPrice = ref(0); //текущие мин значение
const maxPrice = ref(1000); //текущие макс значение


const isGrid = ref(true); 
const items = ref([
    { id: 1, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 2, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 3, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 4, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 5, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 6, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 7, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 8, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 9, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
    { id: 10, code: '15640682', title: 'Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348', image: '/path/to/image.jpg' },
]);

const visibleItems = ref(10); // Начальное количество отображаемых элементов
const isLoading = ref(false); // Для предотвращения множественных запросов

const loadMoreItems = () => {
  if (isLoading.value) return; // Если уже загружаем, выходим
  isLoading.value = true;

  setTimeout(() => {
    const newItems = [
      { id: items.value.length + 1, code: '15640682', title: 'Новый элемент', image: '/path/to/image.jpg' },
      { id: items.value.length + 2, code: '15640682', title: 'Новый элемент', image: '/path/to/image.jpg' },
    ];
    items.value = [...items.value, ...newItems];
    visibleItems.value += 2; 
    isLoading.value = false;
  }, 1000); 
};

const showGrid = () => {
  isGrid.value = true;
};

const showList = () => {
  isGrid.value = false;
};

const updateMinPrice = (event) => {
  minPrice.value = Math.min(Number(event.target.value), maxPrice.value - 1);
};

const updateMaxPrice = (event) => {
  maxPrice.value = Math.max(Number(event.target.value), minPrice.value + 1);
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
        <div class="grid grid-cols-4 gap-4 mt-4"> 
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
            </div>
            <div class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md">
                <div class="font-bold">Шуроповерты</div>
                <p class="text-gray-400 text-sm">4 754 товара</p>
                <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
            </div>
        </div>

        <div class="flex flex-col md:flex-row p-4 mt-4">
            <p class="flex-1 font-bold mr-2">Найдено 4945 товаров</p>
            <div class="flex space-x-2 flex-grow items-center">
                <p>Сортировать по:</p>
                <button class="px-4 py-2">По популярности</button>
                <button class="px-4 py-2">Отзывам</button>
                <button class="px-4 py-2">Рейтингу</button>
                <button class="px-4 py-2">Цене</button>
                <div class="flex justify-end w-full mb-4">
                    <button @click="showGrid" class="mr-2">
                        <img src="/public/interface-design-structure-outline.svg" alt="" class="w-5 h-5" />
                    </button>
                    <button @click="showList">
                        <img src="/public/four-squares-button-of-view-options.svg" alt="" class="w-5 h-5 mr-2 filter text-red-500" />
                    </button>
                </div>
            </div>
        </div>

        <div class="flex mt-4">
            <div class="w-1/4 pr-4">
                <!-- Категория -->
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

                <!-- Цена -->
                <div class="mb-4">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Цена, ₽</span>
                        <button
                            @click="resetPrice"
                            class="h-10 px-4 py-2 flex items-center"
                        >
                            Сбросить
                        </button>
                    </div>

                    <div class="flex justify-between items-center">
                        <input type="text" value="от 9 760" class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2" />
                        <input type="text" value="до 10 281 483" class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2" />
                    </div>
                    <div class="p-4">
                        <div class="p-4">
                    <div class="flex justify-between mb-4">
                        <span>От: {{ minPrice }}</span>
                        <span>До: {{ maxPrice }}</span>
                    </div>
                
                    <div class="relative h-8">
                        <!-- Фоновая линия -->
                        <div class="absolute w-full h-2 bg-gray-200 rounded-full top-1/2 transform -translate-y-1/2"></div>
                
                        <!-- Красная линия между ползунками -->
                        <div
                        class="absolute h-2 bg-red-500 rounded-full top-1/2 transform -translate-y-1/2"
                        :style="{
                            left: `${((minPrice - min) / (max - min)) * 100}%`,
                            width: `${((maxPrice - minPrice) / (max - min)) * 100}%`,
                        }"
                        ></div>
                
                        <!-- Ползунок для минимальной цены -->
                        <input
                        type="range"
                        :min="min"
                        :max="max"
                        v-model.number="minPrice"
                        @input="updateMinPrice"
                        class="absolute w-full appearance-none pointer-events-none"
                        />
                        <!-- Ползунок для максимальной цены -->
                        <input
                        type="range"
                        :min="min"
                        :max="max"
                        v-model.number="maxPrice"
                        @input="updateMaxPrice"
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
                    <div class="">
                        <div class="flex">
                            <button>Все фильтры</button>
                            <img src="" alt="Фильтр" class="" />
                        </div>
                        <button class="text-white bg-red-600 rounded-2xl p-2">Показать 166 товаров</button>
                        <div class="flex">
                            <img src=""  alt="крест" class=""/>
                            <space>Сбросить все фильтры</space>
                        </div>
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
                            <div class="flex">
                                <p>код: {{ item.code }}</p>
                                <img src="" alt="" class="" />
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

<style>
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
</style>


<style scoped>
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
  </style>