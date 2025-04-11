<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Отзывы товара</h2>

    <div class="flex flex-col md:flex-row gap-8 mb-8">
      <div class="w-full md:w-1/3 bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Общая оценка</h3>
        <div class="flex items-center mb-4">
          <div class="text-4xl font-bold mr-4 text-gray-900">4.9</div>
          <div>
            <div class="flex mb-1">
              <StarIcon v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" />
            </div>
            <div class="text-sm text-gray-500">На основе {{ reviews.length }} отзывов</div>
          </div>
        </div>

        <div class="space-y-3">
          <div v-for="i in 5" :key="`rating-${i}`" class="flex items-center group">
            <span class="w-16 text-sm text-gray-600 group-hover:text-gray-900 transition-colors"
              >{{ 6 - i }} звезд</span
            >
            <div class="flex-1 mx-2 h-2.5 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full bg-yellow-400 transition-all duration-500"
                :style="{
                  width: `${getRatingPercentage(6 - i)}%`,
                }"
              ></div>
            </div>
            <span class="text-sm text-gray-500 w-8 text-right"> {{ getRatingPercentage(6 - i) }}% </span>
          </div>
        </div>
      </div>

      <!-- Основной контент -->
      <div class="w-full md:w-2/3">
        <!-- Галерея фотографий -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold mb-4 text-gray-800">Фотографии товаров</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div
              v-for="(photo, index) in customerPhotos"
              :key="index"
              class="relative aspect-square overflow-hidden rounded-lg cursor-pointer hover:shadow-md transition-shadow"
              @click="openGallery(index)"
            >
              <img
                :src="photo"
                alt="Фото покупателя"
                class="w-full h-full object-cover transition-transform hover:scale-105"
                loading="lazy"
              />
              <div
                class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity"
              ></div>
            </div>
          </div>
        </div>

        <!-- Сортировка и отзывы -->
        <div class="bg-white p-5 rounded-lg shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h3 class="text-lg font-semibold text-gray-800">Все отзывы ({{ reviews.length }})</h3>
            <div class="flex flex-col w-full sm:w-48">
              <label for="sort" class="text-sm font-medium text-gray-700 mb-1 sr-only">Сортировать по:</label>
              <select
                id="sort"
                v-model="sortBy"
                class="border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors cursor-pointer"
              >
                <option value="newest">Сначала новые</option>
                <option value="oldest">Сначала старые</option>
                <option value="highest">Сначала высокие оценки</option>
                <option value="lowest">Сначала низкие оценки</option>
              </select>
            </div>
          </div>

          <div class="space-y-6">
            <div
              v-for="review in sortedReviews"
              :key="review.id"
              class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0"
            >
              <div class="flex items-center mb-2">
                <div class="flex mr-2">
                  <StarIcon
                    v-for="i in 5"
                    :key="i"
                    class="w-4 h-4"
                    :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'"
                  />
                </div>
                <span class="text-sm font-medium text-gray-700">{{ review.rating }}.0</span>
              </div>
              <h4 class="font-semibold text-gray-900">{{ review.author }}</h4>
              <p class="text-gray-500 text-sm mb-2">{{ formatDate(review.date) }}</p>
              <p class="mb-3 text-gray-700">{{ review.text }}</p>

              <div v-if="review.photos && review.photos.length" class="flex flex-wrap gap-2 mt-3">
                <div
                  v-for="(photo, i) in review.photos"
                  :key="i"
                  class="relative w-16 h-16 rounded overflow-hidden cursor-pointer hover:shadow-md transition-shadow"
                  @click="openGalleryFromReview(review.photos, i)"
                >
                  <img :src="photo" alt="Фото из отзыва" class="w-full h-full object-cover" loading="lazy" />
                  <div class="absolute inset-0 bg-black/10 opacity-0 hover:opacity-100 transition-opacity"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <GalleryModal
      v-if="gallery.active"
      :images="gallery.images"
      :initialIndex="gallery.index"
      @close="gallery.active = false"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Компоненты
const StarIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
    </svg>
  `,
}

const GalleryModal = {
  props: ['images', 'initialIndex'],
  emits: ['close'],
  setup(props, { emit }) {
    const currentIndex = ref(props.initialIndex || 0)

    const nextImage = () => {
      currentIndex.value = (currentIndex.value + 1) % props.images.length
    }

    const prevImage = () => {
      currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length
    }

    return { currentIndex, nextImage, prevImage, emit }
  },
}

// Данные
const customerPhotos = ref([
  'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
])

const reviews = ref([
  {
    id: 1,
    author: 'Алексей Петров',
    rating: 5,
    date: '2023-05-15',
    text: 'Отличный товар, полностью соответствует описанию. Качество на высоте, всем рекомендую! Доставка была быстрой, упаковка целая. Очень доволен покупкой и буду заказывать ещё.',
    photos: [
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
    ],
  },
  {
    id: 2,
    author: 'Мария Иванова',
    rating: 4,
    date: '2023-04-28',
    text: 'Хороший товар, но есть небольшие недочеты. В целом довольна покупкой. Цена соответствует качеству. Минус один звезду за небольшие царапины на корпусе, но это не критично.',
    photos: [],
  },
  {
    id: 3,
    author: 'Дмитрий Смирнов',
    rating: 5,
    date: '2023-06-02',
    text: 'Прекрасное качество, быстрая доставка. Буду заказывать еще! Все работает идеально, выглядит даже лучше чем на фото. Продавец ответственный, на все вопросы ответил быстро.',
    photos: [
      'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
      'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
    ],
  },
  {
    id: 4,
    author: 'Елена Козлова',
    rating: 5,
    date: '2023-06-10',
    text: 'Лучшее, что я покупала за последнее время! Функционал превзошел все ожидания. Пользуюсь уже месяц - никаких нареканий. Очень удобный и стильный.',
    photos: [],
  },
  {
    id: 5,
    author: 'Иван Сидоров',
    rating: 3,
    date: '2023-05-30',
    text: 'Неплохо, но ожидал большего за эти деньги. Качество сборки среднее, некоторые элементы люфтят. В целом работает, но есть ощущение, что могло быть и лучше.',
    photos: [],
  },
])

const sortBy = ref('newest')

const gallery = ref({
  active: false,
  images: [],
  index: 0,
})

const sortedReviews = computed(() => {
  const reviewsCopy = [...reviews.value]

  switch (sortBy.value) {
    case 'newest':
      return reviewsCopy.sort((a, b) => new Date(b.date) - new Date(a.date))
    case 'oldest':
      return reviewsCopy.sort((a, b) => new Date(a.date) - new Date(b.date))
    case 'highest':
      return reviewsCopy.sort((a, b) => b.rating - a.rating)
    case 'lowest':
      return reviewsCopy.sort((a, b) => a.rating - b.rating)
    default:
      return reviewsCopy
  }
})

const getRatingPercentage = rating => {
  if (!reviews.value.length) return 0
  const count = reviews.value.filter(r => r.rating === rating).length
  return Math.round((count / reviews.value.length) * 100)
}

const formatDate = dateString => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('ru-RU', options)
}

const openGallery = index => {
  gallery.value = {
    active: true,
    images: customerPhotos.value,
    index: index,
  }
}

const openGalleryFromReview = (images, index) => {
  gallery.value = {
    active: true,
    images: images,
    index: index,
  }
}
</script>
