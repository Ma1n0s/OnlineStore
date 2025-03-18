<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const isGrid = ref(false); // По умолчанию список
const items = ref([
  { id: 1, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 2, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 3, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 4, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 5, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 6, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 7, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 8, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  { id: 9, code: "15640682", title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348", image: "/path/to/image.jpg" },
  {
    id: 10,
    code: "15640682",
    title: "Дрель-шуруповерт Ryobi ONE+ R18DD3-220S 5133003348",
    image: "/path/to/image.jpg",
  },
]);

const visibleItems = ref(10);
const isLoading = ref(false);

const loadMoreItems = () => {
  if (isLoading.value) return;
  isLoading.value = true;

  setTimeout(() => {
    const newItems = [
      { id: items.value.length + 1, code: "15640682", title: "Новый элемент", image: "/path/to/image.jpg" },
      { id: items.value.length + 2, code: "15640682", title: "Новый элемент", image: "/path/to/image.jpg" },
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
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex space-x-2 text-gray-600 mb-4">
      <RouterLink to="#" class="hover:underline">Главная</RouterLink>
      <p>/</p>
      <RouterLink to="#" class="font-semibold">Инструменты</RouterLink>
      <p>/</p>
      <RouterLink to="#" class="font-semibold">Шуруповерты</RouterLink>
    </nav>
    <div class="flex items-center space-x-2">
      <h2 class="font-bold text-2xl">Шуруповерты</h2>
      <p>4945 товаров</p>
    </div>

    <!-- Сетка из 4 колонок -->
    <div class="grid grid-cols-4 gap-4 mt-4">
      <div
        class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
      >
        <div class="font-bold">Шуроповерты</div>
        <p class="text-gray-400 text-sm">4 754 товара</p>
        <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
      </div>
      <div
        class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
      >
        <div class="font-bold">Шуроповерты</div>
        <p class="text-gray-400 text-sm">4 754 товара</p>
        <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
      </div>
      <div
        class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
      >
        <div class="font-bold">Шуроповерты</div>
        <p class="text-gray-400 text-sm">4 754 товара</p>
        <img src="/public/15.webp" alt="alt" class="w-full h-32 object-cover rounded-md" />
      </div>
      <div
        class="bg-gray-100 flex flex-col space-y-2 justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
      >
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
            <img
              src="/public/four-squares-button-of-view-options.svg"
              alt=""
              class="w-5 h-5 mr-2 filter text-red-500"
            />
          </button>
        </div>
      </div>
    </div>

    <div class="flex mt-4">
      <div class="w-1/4 pr-4">
        <!-- Категория -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Категория</label>
          <input type="text" value="Инструмент" class="mt-1 block w-full border border-gray-300 rounded-md p-2" />
          <input
            type="text"
            value="Генераторы (электростанции)"
            class="mt-1 block w-full border border-gray-300 rounded-md p-2"
          />
        </div>

        <!-- Цена -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Цена, ₽</label>
          <div class="flex justify-between items-center">
            <input type="text" value="от 9 760" class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2" />
            <input type="text" value="до 10 281 483" class="mt-1 block w-1/2 border border-gray-300 rounded-md p-2" />
          </div>
          <div class="h-2 bg-red-500 mt-2 rounded-md w-full"></div>
        </div>
        <div class="mb-4">
          <h2 class="text-xl font-bold mb-4">Производители</h2>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Aurora
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Champion
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                DDE
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                EuroPower
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Gigant
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Inforce
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Patriot
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Спец
              </label>
            </div>
            <div class="space-y-2">
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                CARVER
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                DAEWOO
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Elitech
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                FUBAG
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                Huter
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                LIFAN
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                ЗУБР
              </label>
              <label class="flex items-center">
                <input type="checkbox" class="mr-2" />
                TCC
              </label>
            </div>
          </div>
          <div class="mt-4">
            <button class="bg-blue-500 text-white py-2 px-4 rounded">Показать еще 68</button>
          </div>
        </div>
      </div>

      <div class="w-3/4">
        <div :class="isGrid ? 'grid grid-cols-4 gap-4' : 'flex flex-col space-y-4'">
          <div
            class="bg-white shadow-md rounded-xl border-2 border-gray-200 overflow-hidden"
            :class="isGrid ? 'w-full' : 'w-full'"
            v-for="(item, index) in items.slice(0, visibleItems)"
            :key="item.id"
          >
            <div class="p-4">
              <div class="flex">
                <p>код: {{ item.code }}</p>
                <img src="" alt="" class="" />
              </div>
              <img :src="item.image" alt="Дрель-шуруповерт Ryobi ONE+" class="w-full h-40 object-cover" />
              <h3 class="mt-2 text-xl font-semibold">{{ item.title }}</h3>
              <p class="text-green-500">В наличии > 100 шт.</p>

              <div class="flex flex-col space-y-2">
                <div class="flex justify-between">
                  <span class="font-medium">Самовывоз:</span>
                  <span class="flex-1 text-right">сегодня</span>
                </div>
                <div class="flex justify-between">
                  <span class="font-medium">Курьером:</span>
                  <span class="flex-1 text-right">сегодня</span>
                </div>
              </div>
              <span class="line-through text-gray-400 mr-2">15 990 ₽</span>
              <span class="bg-green-600 w-10 h-10 p-1 rounded-l-sm text-white items-center">-17%</span>
              <span class="flex text-red-500">13 290 ₽</span>
              <button class="flex bg-red-600 text-white py-2 px-4 rounded mt-2 hover:bg-red-700">В корзину</button>
            </div>
          </div>
        </div>

        <!-- Кнопка "Показать ещё" -->
        <div class="flex justify-center mt-4" v-if="visibleItems < items.length">
          <button @click="loadMoreItems" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
            Показать ещё
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
