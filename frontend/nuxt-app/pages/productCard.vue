<script setup>
import { ref } from "vue";

const product = reactive({
  title: "Бесщеточный аккумуляторный шуруповерт KEYANG DD18BL-W (Set)",
  code: "24955389",
  reviewsCount: 14,
  questionsCount: 5,
  warranty: "1 год",
  brand: "KEYANG",
  description: "Бесщеточный аккумуляторный шуруповерт KEYANG DD18BL-W (Set)",
  price: {
    original: 8990,
    final: 7490,
    savings: 1500,
  },
  images: [
    "/products/keyang-dd18bl-w/main.jpg",
    "/products/keyang-dd18bl-w/1.jpg",
    "/products/keyang-dd18bl-w/2.jpg",
    "/products/keyang-dd18bl-w/3.jpg",
    "/products/keyang-dd18bl-w/4.jpg",
  ],
  specifications: {
    maxTorque: "60 Н·м",
    batteryType: "Li-Ion",
    batteryVoltage: "18 В",
    maxDrillDiameterMetal: "10 мм",
    maxDrillDiameterWood: "25 мм",
    chargerIncluded: "есть",
    netWeight: "3.5 кг",
  },
  advantages: [
    { parameter: "Тип", value: "Аккумуляторный шуруповерт" },
    { parameter: "Макс. частота ударов", value: "0 ударов/мин" },
    { parameter: "Количество скоростей", value: "2" },
    { parameter: "Емкость аккумулятора", value: "2.0 А·ч" },
    { parameter: "Время зарядки", value: "1 ч" },
    { parameter: "Реверс", value: "есть" },
    { parameter: "Патрон", value: "быстрозажимной" },
    { parameter: "Диаметр патрона", value: "13 мм" },
  ],
  packagingDetails: {
    weight: "3.5",
    length: "350",
    width: "250",
    height: "100",
  },
});

// Вкладки
const tabs = ref([
  { id: "description", title: "ОПИСАНИЕ И ХАРАКТЕРИСТИКИ" },
  { id: "reviews", title: "ОТЗЫВЫ" },
  { id: "questions", title: "ВОПРОСЫ И ОТВЕТЫ" },
]);
const activeTab = ref("description");
const isFavorite = ref(false);
const loading = ref(false);
const activeImage = ref(product.images[0]);

const toggleFavorite = async () => {
  isFavorite.value = !isFavorite.value;
};
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="#" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Инструменты</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Шуруповерты</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Аккумуляторные дрели-шуруповерты</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Безударные</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">KEYANG</NuxtLink>
    </nav>

    <div v-if="!loading">
      <h2 class="font-bold text-2xl mb-2">{{ product.title }}</h2>
      <div class="flex flex-wrap gap-4 mb-6">
        <p class="text-sm" v-if="productCode">Код товара: {{ product.code }}</p>
        <div class="flex items-center gap-2" v-if="product.reviewsCount || product.questionsCount">
          <img src="" alt="Отзыв" class="w-4 h-4" />
          <p class="text-sm">
            <span v-if="product.reviewsCount">{{ product.reviewsCount }} отзывов</span>
            <span v-if="product.reviewsCount && product.questionsCount"> | </span>
            <span v-if="product.questionsCount">{{ product.questionsCount }} вопросов</span>
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
            <NuxtImg :src="activeImage" :alt="product.title" class="w-full h-80 object-contain mb-4" />
            <div class="flex gap-2 overflow-x-auto">
              <NuxtImg
                v-for="(image, index) in product.images"
                :key="index"
                :src="image"
                :alt="'Изображение ' + (index + 1)"
                class="w-16 h-16 object-cover rounded cursor-pointer border"
                :class="{ 'border-primary': image === activeImage }"
                @click="activeImage = image"
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

          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
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
        <div v-show="activeTab === 'description'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2">
            <div v-if="product.description" class="mb-8">
              <h3 class="text-xl font-bold mb-4">Описание</h3>
              <div>
                {{ product.description }}
              </div>
            </div>

            <div class="mb-8">
              <h3 class="text-xl font-bold mb-4">Технические характеристики</h3>
              <div class="overflow-x-auto">
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
                    <tr v-for="(desc, index) in product.advantages" :key="index">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-dark">{{ desc.parameter }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray">{{ desc.value }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div>
            <div class="rounded-lg p-6 mb-6">
              <h3 class="text-xl font-bold mb-4">{{ product.brand }}</h3>
              <p class="text-gray mb-4">Все товары бренда</p>

              <div v-if="product.brand" class="space-y-3">
                <div class="flex items-center">
                  <img src="" alt="Южная Корея" class="w-5 h-5 mr-2" />
                  <span class="text-dark">Южная Корея — родина бренда</span>
                </div>
                <div class="flex items-center">
                  <img src="" alt="Китай" class="w-5 h-5 mr-2" />
                  <span class="text-dark">Китай — страна производства</span>
                </div>
              </div>
            </div>

            <div v-if="packagingInfo.length" class="rounded-lg p-6 mb-6">
              <h3 class="text-xl font-bold mb-4">Комплектация</h3>
              <ul class="list-disc pl-5 space-y-2">
                <li v-for="(item, index) in packagingInfo" :key="index">{{ item }}</li>
              </ul>
            </div>

            <div class="rounded-lg p-6">
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
            </div>
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
