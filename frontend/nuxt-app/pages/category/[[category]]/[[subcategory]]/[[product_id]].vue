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
  { id: "reviews", title: "ОТЗЫВЫ" },
  { id: "questions", title: "ВОПРОСЫ И ОТВЕТЫ" },
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
                <button class="bg-second hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition">
                  В корзину
                </button>
                <button class="bg-gray-200 hover:bg-gray-300 text-dark py-3 px-6 rounded-lg font-medium transition">
                  Быстрый заказ
                </button>
              </div>
            </div>
          </div>

          <!-- <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold mb-4">Основные характеристики</h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <li v-if="product.specifications.maxTorque" class="flex gap-2">
                <span class="font-medium text-gray">Макс. крутящий момент:</span>
                <span>{{ product.specifications.maxTorque }}</span>
              </li>
              <li v-if="product.specifications.batteryType" class="flex gap-2">
                <span class="font-medium text-gray">Тип аккумулятора:</span>
                <span>{{ product.specifications.batteryType }}</span>
              </li>
              <li v-if="product.specifications.batteryVoltage" class="flex gap-2">
                <span class="font-medium text-gray">Напряжение аккумулятора:</span>
                <span>{{ product.specifications.batteryVoltage }}</span>
              </li>
              <li v-if="product.specifications.maxDrillDiameterMetal" class="flex gap-2">
                <span class="font-medium text-gray">Макс. диаметр сверления (металл):</span>
                <span>{{ product.specifications.maxDrillDiameterMetal }}</span>
              </li>
              <li v-if="product.specifications.maxDrillDiameterWood" class="flex gap-2">
                <span class="font-medium text-gray">Макс. диаметр сверления (дерево):</span>
                <span>{{ product.specifications.maxDrillDiameterWood }}</span>
              </li>
              <li v-if="product.specifications.chargerIncluded" class="flex gap-2">
                <span class="font-medium text-gray">Зарядное устройство:</span>
                <span>{{ product.specifications.chargerIncluded }}</span>
              </li>
              <li v-if="product.specifications.netWeight" class="flex gap-2">
                <span class="font-medium text-gray">Вес нетто:</span>
                <span>{{ product.specifications.netWeight }}</span>
              </li>
            </ul>
          </div> -->
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
        <div v-show="activeTab === 'description'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2">
            <div v-if="product.description" class="mb-8">
              <h3 class="text-xl font-bold mb-4">Описание</h3>
              <div>
                {{ product.description.slice(0, 600) }}
              </div>
            </div>

            <div class="mb-8">
              <h3 class="text-xl font-bold mb-4">Технические характеристики</h3>
              <div class="overflow-x-auto w-full">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray uppercase tracking-wider">
                        Параметр
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray uppercase tracking-wider">
                        Значение
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <template v-for="(category, name) in product?.specifications" :key="name">
                      <tr class="bg-gray-50">
                        <td colspan="2" class="px-6 py-3 text-sm uppercase font-bold text-gray-900">
                          {{ name }}
                        </td>
                      </tr>
                      <tr v-for="(title, value) in category" :key="title">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-dark">{{ value }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-dark">{{ title }}</td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div>
            <div class="rounded-lg p-6 mb-6">
              <h3 class="text-xl font-bold mb-4">{{ product.brand }}</h3>
              <p class="text-gray mb-4">Все товары бренда</p>

              <!-- <div v-if="product.brand" class="space-y-3">
                <div class="flex items-center">
                  <img src="" alt="Южная Корея" class="w-5 h-5 mr-2" />
                  <span class="text-dark">Южная Корея — родина бренда</span>
                </div>
                <div class="flex items-center">
                  <img src="" alt="Китай" class="w-5 h-5 mr-2" />
                  <span class="text-dark">Китай — страна производства</span>
                </div>
              </div> -->
            </div>

            <!-- <div v-if="packagingInfo.length" class="rounded-lg p-6 mb-6">
              <h3 class="text-xl font-bold mb-4">Комплектация</h3>
              <ul class="list-disc pl-5 space-y-2">
                <li v-for="(item, index) in packagingInfo" :key="index">{{ item }}</li>
              </ul>
            </div> -->

            <!-- <div class="rounded-lg p-6">
              <h3 class="text-xl font-bold mb-4">Информация об упаковке</h3>
              <div class="space-y-2">
                <p v-for="(value, key) in product.packagingDetails" :key="key" class="text-dark">
                  {{
                    key === "weight"
                      ? "Вес, кг"
                      : key === "length"
                      ? "Длина, мм"
                      : key === "width"
                      ? "Ширина, мм"
                      : "Высота, мм"
                  }}: {{ value }}
                </p>
              </div>
            </div> -->
          </div>
        </div>

        <div v-show="activeTab === 'reviews'">
          <p>Отзывы о товаре</p>
        </div>

        <div v-show="activeTab === 'questions'">
          <p>Вопросы и ответы</p>
        </div>
      </div>
    </div>

    <div v-else class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
  </div>
</template>
