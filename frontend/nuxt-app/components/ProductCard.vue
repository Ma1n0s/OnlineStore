<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination, Navigation, Thumbs, FreeMode } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/navigation'
import 'swiper/css/thumbs'
import 'swiper/css/free-mode'


// Параметры маршрута
const route = useRoute()
const productId = route.params.id

// Состояния компонента
const thumbsSwiper = ref(null)
const activeImageIndex = ref(0)
const quantity = ref(1)
const selectedColor = ref(null)
const selectedSize = ref(null)
const activeTab = ref('description')
const isFavorite = ref(false)
const isAddedToCart = ref(false)

// Данные продукта (в реальном проекте - загрузка из API)
const product = ref({
  id: productId,
  name: 'Смартфон Samsung Galaxy S23 Ultra 12/512GB',
  price: 109999,
  oldPrice: 119999,
  discount: 8,
  rating: 4.8,
  reviewsCount: 124,
  inStock: true,
  colors: [
    { id: 1, name: 'Черный', value: 'bg-gray-900', available: true },
    { id: 2, name: 'Зеленый', value: 'bg-green-700', available: true },
    { id: 3, name: 'Бежевый', value: 'bg-amber-200', available: false },
  ],
  sizes: [
    { id: 1, name: '128GB', available: true },
    { id: 2, name: '256GB', available: true },
    { id: 3, name: '512GB', available: true },
    { id: 4, name: '1TB', available: false },
  ],
  images: [
    'https://via.placeholder.com/800x800/F3F4F6/2563EB?text=Galaxy+S23+1',
    'https://via.placeholder.com/800x800/F3F4F6/2563EB?text=Galaxy+S23+2',
    'https://via.placeholder.com/800x800/F3F4F6/2563EB?text=Galaxy+S23+3',
    'https://via.placeholder.com/800x800/F3F4F6/2563EB?text=Galaxy+S23+4',
  ],
  description: 'Флагманский смартфон Samsung Galaxy S23 Ultra с революционной камерой 200 Мп и мощнейшим процессором Snapdragon 8 Gen 2.',
  features: [
    { name: 'Экран', value: '6.8" Dynamic AMOLED 2X, 120Hz' },
    { name: 'Процессор', value: 'Qualcomm Snapdragon 8 Gen 2' },
    { name: 'Оперативная память', value: '12 ГБ' },
    { name: 'Память', value: '512 ГБ' },
    { name: 'Основная камера', value: '200 Мп + 12 Мп + 10 Мп + 10 Мп' },
    { name: 'Фронтальная камера', value: '12 Мп' },
    { name: 'Аккумулятор', value: '5000 мАч' },
    { name: 'ОС', value: 'Android 13' },
  ],
  specifications: [
    { name: 'Вес', value: '234 г' },
    { name: 'Размеры', value: '163.4 x 78.1 x 8.9 мм' },
    { name: 'Материал корпуса', value: 'Стекло, алюминий' },
    { name: 'Защита', value: 'IP68' },
    { name: 'Сетевые технологии', value: '5G, LTE, Wi-Fi 6E' },
    { name: 'Беспроводные технологии', value: 'Bluetooth 5.3, NFC' },
    { name: 'Разъем', value: 'USB Type-C' },
    { name: 'Датчики', value: 'Акселерометр, гироскоп, компас, сканер отпечатков' },
  ],
  deliveryOptions: [
    { name: 'Самовывоз', value: 'Сегодня', price: 0 },
    { name: 'Курьером', value: 'Завтра', price: 490 },
    { name: 'Почта России', value: '3-5 дней', price: 350 },
  ],
})

// Инициализация выбранных вариантов
selectedColor.value = product.value.colors.find(c => c.available)
selectedSize.value = product.value.sizes.find(s => s.available)

// Форматирование цены
const formattedPrice = computed(() => {
  return (product.value.price / 100).toLocaleString('ru-RU') + ' ₽'
})

const formattedOldPrice = computed(() => {
  return (product.value.oldPrice / 100).toLocaleString('ru-RU') + ' ₽'
})

// Добавление в корзину
const addToCart = () => {
  isAddedToCart.value = true
  setTimeout(() => {
    isAddedToCart.value = false
  }, 3000)
  // Здесь обычно API запрос
}

// Переключение избранного
const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  // Здесь обычно API запрос
}

// Swiper модули
const modules = [Pagination, Navigation, Thumbs, FreeMode]
</script>

<template>
  <div class="bg-white">
    <!-- Хлебные крошки -->
    <nav class="bg-gray-50 border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-4">
      <div class="max-w-7xl mx-auto flex items-center space-x-2 text-sm">
        <NuxtLink to="/" class="text-gray-500 hover:text-gray-700">Главная</NuxtLink>
        <ChevronRightIcon class="h-4 w-4 text-gray-400" />
        <NuxtLink to="/catalog" class="text-gray-500 hover:text-gray-700">Смартфоны</NuxtLink>
        <ChevronRightIcon class="h-4 w-4 text-gray-400" />
        <span class="text-gray-700 font-medium">{{ product.name }}</span>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
        <!-- Галерея изображений -->
        <div class="flex flex-col-reverse">
          <!-- Миниатюры -->
          <div class="mt-4 hidden lg:block">
            <swiper
              @swiper="setThumbsSwiper"
              :spaceBetween="10"
              :slidesPerView="4"
              :freeMode="true"
              :watchSlidesProgress="true"
              :modules="modules"
              class="thumbs-swiper"
            >
              <swiper-slide v-for="(image, index) in product.images" :key="index">
                <button
                  @click="activeImageIndex = index"
                  class="block w-full h-24 rounded-md overflow-hidden border-2"
                  :class="activeImageIndex === index ? 'border-primary' : 'border-transparent'"
                >
                  <img :src="image" :alt="`${product.name} - ${index + 1}`" class="w-full h-full object-cover" />
                </button>
              </swiper-slide>
            </swiper>
          </div>

          <!-- Основной слайдер -->
          <div class="relative rounded-lg overflow-hidden">
            <swiper
              :pagination="{ clickable: true }"
              :navigation="{
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
              }"
              :thumbs="{ swiper: thumbsSwiper }"
              :modules="modules"
              class="main-swiper"
              @slideChange="(swiper) => activeImageIndex = swiper.activeIndex"
            >
              <swiper-slide v-for="(image, index) in product.images" :key="index">
                <div class="aspect-w-1 aspect-h-1 bg-gray-100">
                  <img :src="image" :alt="`${product.name} - ${index + 1}`" class="w-full h-full object-contain" />
                </div>
              </swiper-slide>
            </swiper>

            <!-- Кастомные кнопки навигации -->
            <button class="swiper-button-prev">
              <ChevronLeftIcon class="h-6 w-6 text-white" />
            </button>
            <button class="swiper-button-next">
              <ChevronRightIcon class="h-6 w-6 text-white" />
            </button>
          </div>
        </div>

        <!-- Информация о продукте -->
        <div class="mt-8 lg:mt-0">
          <div class="flex justify-between items-start">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
              {{ product.name }}
            </h1>
            <button
              @click="toggleFavorite"
              type="button"
              class="ml-4 flex items-center justify-center p-2 rounded-full"
              :class="isFavorite ? 'text-red-500' : 'text-gray-400 hover:text-gray-500'"
            >
              <HeartIcon class="h-6 w-6" :class="{ 'fill-current': isFavorite }" />
            </button>
          </div>

          <!-- Рейтинг и отзывы -->
          <div class="mt-4 flex items-center">
            <div class="flex items-center">
              <StarIcon
                v-for="i in 5"
                :key="i"
                class="h-5 w-5 flex-shrink-0"
                :class="i <= Math.floor(product.rating) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 fill-gray-300'"
              />
            </div>
            <span class="ml-2 text-sm text-gray-500">
              {{ product.rating }} ({{ product.reviewsCount }} отзывов)
            </span>
          </div>

          <!-- Цены -->
          <div class="mt-4">
            <p class="text-3xl font-bold text-gray-900">{{ formattedPrice }}</p>
            <div class="flex items-center mt-1">
              <p class="text-lg text-gray-500 line-through">{{ formattedOldPrice }}</p>
              <span class="ml-2 bg-red-100 text-red-800 text-sm font-medium px-2 py-0.5 rounded">
                -{{ product.discount }}%
              </span>
            </div>
          </div>

          <!-- Наличие -->
          <div class="mt-4 flex items-center">
            <CheckIcon v-if="product.inStock" class="h-5 w-5 text-green-500" />
            <span class="ml-2 text-sm" :class="product.inStock ? 'text-green-600' : 'text-red-600'">
              {{ product.inStock ? 'В наличии' : 'Нет в наличии' }}
            </span>
          </div>

          <!-- Описание -->
          <div class="mt-6">
            <p class="text-base text-gray-700">{{ product.description }}</p>
          </div>

          <!-- Цвета -->
          <div class="mt-8">
            <h3 class="text-sm font-medium text-gray-900">Цвет</h3>
            <div class="mt-2 flex flex-wrap gap-3">
              <button
                v-for="color in product.colors"
                :key="color.id"
                @click="selectedColor = color"
                type="button"
                class="relative flex items-center justify-center rounded-full p-0.5 focus:outline-none"
                :class="[
                  color.available ? 'cursor-pointer' : 'cursor-not-allowed opacity-50',
                  selectedColor.id === color.id ? 'ring-2 ring-primary' : 'ring-1 ring-gray-300',
                ]"
                :disabled="!color.available"
                :title="color.available ? color.name : 'Нет в наличии'"
              >
                <span class="sr-only">{{ color.name }}</span>
                <span
                  aria-hidden="true"
                  class="h-8 w-8 rounded-full border border-black border-opacity-10"
                  :class="[color.value]"
                />
              </button>
            </div>
          </div>

          <!-- Объем памяти -->
          <div class="mt-8">
            <h3 class="text-sm font-medium text-gray-900">Объем памяти</h3>
            <div class="mt-2 flex flex-wrap gap-3">
              <button
                v-for="size in product.sizes"
                :key="size.id"
                @click="selectedSize = size"
                type="button"
                class="px-4 py-2 text-sm rounded-md border focus:outline-none"
                :class="[
                  size.available ? 'cursor-pointer' : 'cursor-not-allowed opacity-50',
                  selectedSize.id === size.id
                    ? 'bg-primary text-white border-primary'
                    : 'bg-white text-gray-900 border-gray-300 hover:bg-gray-50',
                ]"
                :disabled="!size.available"
                :title="size.available ? size.name : 'Нет в наличии'"
              >
                {{ size.name }}
              </button>
            </div>
          </div>

          <!-- Количество и кнопка добавления -->
          <div class="mt-8">
            <div class="flex items-center space-x-4">
              <div class="flex items-center border border-gray-300 rounded-md">
                <button
                  @click="quantity > 1 ? quantity-- : null"
                  class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-l-md"
                  :class="{ 'opacity-50 cursor-not-allowed': quantity <= 1 }"
                >
                  -
                </button>
                <input
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  class="w-12 text-center border-0 focus:ring-0"
                />
                <button
                  @click="quantity++"
                  class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-r-md"
                >
                  +
                </button>
              </div>
              <button
                @click="addToCart"
                :disabled="!product.inStock || isAddedToCart"
                class="flex-1 bg-primary hover:bg-primary-dark text-white py-3 px-6 rounded-md font-medium transition-colors flex items-center justify-center"
                :class="{
                  'bg-green-500 hover:bg-green-600': isAddedToCart,
                  'opacity-50 cursor-not-allowed': !product.inStock,
                }"
              >
                <ShoppingCartIcon class="h-5 w-5 mr-2" />
                {{
                  isAddedToCart
                    ? 'Добавлено в корзину'
                    : product.inStock
                    ? 'Добавить в корзину'
                    : 'Нет в наличии'
                }}
              </button>
            </div>
          </div>

          <!-- Доставка -->
          <div class="mt-8 border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-900">Варианты доставки</h3>
            <div class="mt-4 space-y-4">
              <div
                v-for="option in product.deliveryOptions"
                :key="option.name"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <input
                    :id="`delivery-${option.name}`"
                    name="delivery-method"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-primary focus:ring-primary"
                    :checked="option.name === 'Самовывоз'"
                  />
                  <label
                    :for="`delivery-${option.name}`"
                    class="ml-2 block text-sm text-gray-700"
                  >
                    {{ option.name }} - {{ option.value }}
                  </label>
                </div>
                <span class="text-sm font-medium text-gray-900">
                  {{ option.price > 0 ? option.price + ' ₽' : 'Бесплатно' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Табы с дополнительной информацией -->
      <div class="mt-16 border-t border-gray-200 pt-8">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8">
            <button
              @click="activeTab = 'description'"
              :class="{
                'border-primary text-primary': activeTab === 'description',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
                  activeTab !== 'description',
              }"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Описание
            </button>
            <button
              @click="activeTab = 'features'"
              :class="{
                'border-primary text-primary': activeTab === 'features',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
                  activeTab !== 'features',
              }"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Характеристики
            </button>
            <button
              @click="activeTab = 'specifications'"
              :class="{
                'border-primary text-primary': activeTab === 'specifications',
                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
                  activeTab !== 'specifications',
              }"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Технические характеристики
            </button>
          </nav>
        </div>

        <!-- Контент табов -->
        <div class="mt-6">
          <!-- Описание -->
          <div v-if="activeTab === 'description'" class="prose prose-sm max-w-none">
            <p>
              Флагманский смартфон Samsung Galaxy S23 Ultra сочетает в себе инновационные технологии и элегантный дизайн.
              Устройство оснащено самым мощным процессором Snapdragon 8 Gen 2, созданным специально для Samsung.
            </p>
            <p class="mt-4">
              6.8-дюймовый Dynamic AMOLED 2X экран с адаптивной частотой обновления до 120 Гц обеспечивает невероятно
              плавную картинку и высокую детализацию. Технология Vision Booster автоматически оптимизирует цветопередачу
              и яркость в зависимости от условий освещения.
            </p>
            <p class="mt-4">
              Основная камера с разрешением 200 Мп позволяет делать фотографии профессионального качества даже при
              слабом освещении. Ночной режим стал еще лучше благодаря увеличенным пикселям и улучшенной стабилизации.
            </p>
          </div>

          <!-- Характеристики -->
          <div v-if="activeTab === 'features'" class="grid sm:grid-cols-2 gap-x-8 gap-y-6">
            <div v-for="(feature, index) in product.features" :key="index" class="border-b border-gray-200 pb-4">
              <dt class="text-sm font-medium text-gray-500">{{ feature.name }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ feature.value }}</dd>
            </div>
          </div>

          <!-- Технические характеристики -->
          <div v-if="activeTab === 'specifications'" class="grid sm:grid-cols-2 gap-x-8 gap-y-6">
            <div v-for="(spec, index) in product.specifications" :key="index" class="border-b border-gray-200 pb-4">
              <dt class="text-sm font-medium text-gray-500">{{ spec.name }}</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ spec.value }}</dd>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style>
.main-swiper {
  @apply h-[400px] lg:h-[500px] rounded-lg;
}

.thumbs-swiper {
  @apply h-[100px] cursor-pointer;
}

.swiper-button-prev,
.swiper-button-next {
  @apply absolute top-1/2 z-10 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black/30 rounded-full hover:bg-black/40 transition-colors;
}

.swiper-button-prev {
  @apply left-4;
}

.swiper-button-next {
  @apply right-4;
}

.swiper-pagination-bullet {
  @apply bg-white opacity-80;
}

.swiper-pagination-bullet-active {
  @apply bg-primary opacity-100;
}
</style>