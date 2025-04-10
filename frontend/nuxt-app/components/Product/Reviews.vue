<template>
  <div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Отзывы товара</h2>

    <div class="flex flex-col md:flex-row gap-8 mb-8">
      <div class="w-full md:w-1/3 bg-gray-50 p-6 rounded-lg">
        <h3 class="text-lg font-semibold mb-4">Общая оценка</h3>
        <div class="flex items-center mb-4">
          <div class="text-4xl font-bold mr-4">4.9</div>
          <div>
            <div class="flex mb-1">
              <StarIcon v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" />
            </div>
            <div class="text-sm text-gray-600">На основе {{ reviews.length }} отзывов</div>
          </div>
        </div>

        <div class="space-y-2">
          <div v-for="i in 5" :key="`rating-${i}`" class="flex items-center">
            <span class="w-8 text-sm">{{ 6 - i }} звезд</span>
            <div class="flex-1 mx-2 h-2 bg-gray-200 rounded-full overflow-hidden">
              <div
                class="h-full bg-yellow-400"
                :style="{
                  width: `${reviews.length ? (reviews.filter(r => r.rating === 6 - i).length / reviews.length) * 100 : 0}%`,
                }"
              ></div>
            </div>
            <span class="text-sm text-gray-600 w-8 text-right">
              {{ Math.round((reviews.filter(r => r.rating === 6 - i).length / reviews.length) * 100) }}%
            </span>
          </div>
        </div>
      </div>

      <div class="w-full md:w-2/3">
        <h3 class="text-lg font-semibold mb-4">Фотографии покупателей</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <img
            v-for="(photo, index) in customerPhotos"
            :key="index"
            :src="photo"
            alt="Фото покупателя"
            class="w-full h-40 object-cover rounded-lg"
          />
        </div>
      </div>
    </div>
    <div class="mb-6">
      <div class="flex flex-col w-full md:w-64">
        <label for="sort" class="text-sm font-medium text-gray-700 mb-1">Сортировать по:</label>
        <select
          id="sort"
          v-model="sortBy"
          class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="newest">Сначала новые</option>
          <option value="oldest">Сначала старые</option>
          <option value="highest">Сначала высокие оценки</option>
          <option value="lowest">Сначала низкие оценки</option>
        </select>
      </div>
    </div>

    <div class="space-y-6">
      <div v-for="review in sortedReviews" :key="review.id" class="border-b border-gray-200 pb-6">
        <div class="flex items-center mb-2">
          <div class="flex mr-2">
            <StarIcon
              v-for="i in 5"
              :key="i"
              class="w-4 h-4"
              :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
            />
          </div>
          <span class="text-sm font-medium">{{ review.rating }}.0</span>
        </div>
        <h4 class="font-semibold">{{ review.author }}</h4>
        <p class="text-gray-600 text-sm mb-2">{{ formatDate(review.date) }}</p>
        <p class="mb-3">{{ review.text }}</p>

        <div v-if="review.photos && review.photos.length" class="flex space-x-2 mt-2">
          <img
            v-for="(photo, i) in review.photos"
            :key="i"
            :src="photo"
            alt="Фото из отзыва"
            class="w-16 h-16 object-cover rounded"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const StarIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
    </svg>
  `,
}

// Пример данных
const customerPhotos = ref([
  'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1542103749-8ef59b94f47e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1554151228-14d9def656e4?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
  'https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
])

const reviews = ref([
  {
    id: 1,
    author: 'Алексей Петров',
    rating: 5,
    date: '2023-05-15',
    text: 'Отличный товар, полностью соответствует описанию. Качество на высоте, всем рекомендую!',
    photos: [
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
    ],
  },
  {
    id: 2,
    author: 'Мария Иванова',
    rating: 4,
    date: '2023-04-28',
    text: 'Хороший товар, но есть небольшие недочеты. В целом довольна покупкой.',
    photos: [],
  },
  {
    id: 3,
    author: 'Дмитрий Смирнов',
    rating: 5,
    date: '2023-06-02',
    text: 'Прекрасное качество, быстрая доставка. Буду заказывать еще!',
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
    text: 'Лучшее, что я покупала за последнее время!',
    photos: [],
  },
  {
    id: 5,
    author: 'Иван Сидоров',
    rating: 3,
    date: '2023-05-30',
    text: 'Неплохо, но ожидал большего за эти деньги.',
    photos: [],
  },
])

const sortBy = ref('newest')

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

const formatDate = dateString => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(dateString).toLocaleDateString('ru-RU', options)
}
</script>
