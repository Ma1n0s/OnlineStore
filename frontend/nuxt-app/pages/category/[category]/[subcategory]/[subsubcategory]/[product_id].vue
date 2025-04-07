<script setup lang="ts">
import { ref, onMounted, reactive } from "vue";
import { productData } from "~/shared/productData";
import type { Product } from "~/types/product.types";
const route = useRoute();

// @ts-ignore
const product = reactive<Product>(productData);

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
  { id: "description", title: "ОПИСАНИЕ И ХАРАКТЕРИСТИКИ" },
  // { id: "reviews", title: "ОТЗЫВЫ" },
  // { id: "questions", title: "ВОПРОСЫ И ОТВЕТЫ" },
]);

const activeTab = ref("description");
const isFavorite = ref(false);
const loading = ref(true);
const activeImage = ref(product?.images?.[0]?.src || "");

onMounted(() => {
  activeTab.value = "description";
  isFavorite.value = false;
  loading.value = false;

  console.log(product.specifications);
});

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value;
};
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="/" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">{{ route.params.category }}</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">{{ route.params.subcategory }}</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">{{ product.name }}</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">{{ product.brand || "Назавние Бренда" }}</NuxtLink>
    </nav>

    <div v-if="!loading">
      <h2 class="font-bold text-2xl mb-2">{{ product.title }}</h2>
      <div class="flex flex-wrap gap-4 mb-6">
        <p class="text-sm" v-if="product.code">Код товара: {{ product.code }}</p>
        <div class="flex items-center gap-2" v-if="product.reviewsCount || product.questionsCount">
          <img src="" alt="Отзыв" class="w-4 h-4" />
          <p class="text-sm">
            <span>10 отзывов</span>
            <span> | </span>
            <span>5 вопросов</span>
          </p>
        </div>

        <div class="flex items-center gap-2" v-if="product.warranty">
          <img src="" alt="Гарантия" class="w-4 h-4" />
          <p class="text-sm">Гарантия производителя {{ product.warranty }}</p>
        </div>

        <div class="flex items-center gap-2 cursor-pointer" @click="toggleFavorite">
          <img :src="isFavorite ? '' : ''" alt="Избранное" class="w-4 h-4" />
          <p class="text-sm">{{ isFavorite ? "В избранном" : "В избранное" }}</p>
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
                <button class="bg-danger hover:bg-gray-300 text-white py-3 px-6 rounded-lg font-medium transition">
                  Быстрый заказ
                </button>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Основные характеристики</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Первая колонка -->
              <div class="space-y-6">
                <div>
                  <h3 class="font-semibold text-gray-700 mb-2">Основные</h3>
                  <ul class="space-y-3">
                    <li class="flex justify-between">
                      <span class="text-gray-500">Модель</span>
                      <span class="font-medium">Galaxy S23 Ultra</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Год выпуска</span>
                      <span class="font-medium">2023</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Цвет</span>
                      <span class="font-medium">Черный</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Материал корпуса</span>
                      <span class="font-medium">Стекло, алюминий</span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Вторая колонка -->
              <div class="space-y-6">
                <div>
                  <h3 class="font-semibold text-gray-700 mb-2">Экран</h3>
                  <ul class="space-y-3">
                    <li class="flex justify-between">
                      <span class="text-gray-500">Диагональ</span>
                      <span class="font-medium">6.8"</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Разрешение</span>
                      <span class="font-medium">3088 × 1440</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Технология</span>
                      <span class="font-medium">Dynamic AMOLED 2X</span>
                    </li>
                    <li class="flex justify-between">
                      <span class="text-gray-500">Частота обновления</span>
                      <span class="font-medium">120 Гц</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
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

            <div class="mb-8">
              <h3 class="text-xl font-bold mb-4">Технические характеристики</h3>
              <div class="overflow-x-auto w-full">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Параметр
                      </th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Значение
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <template v-for="(category, name) in product?.specifications" :key="name">
                      <tr class="bg-gray-50">
                        <td colspan="2" class="px-4 py-2 text-xs uppercase font-bold text-gray-700">
                          {{ name }}
                        </td>
                      </tr>
                      <tr v-for="(title, value) in category" :key="title">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ value }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ title }}</td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
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
