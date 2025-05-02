<template>
  <NuxtLink :to="`/news/${article.id}`" class="block group">
    <div
      class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-200 h-full flex flex-col"
    >
      <div class="h-48 overflow-hidden">
        <img
          :src="article.image || 'no-photo.webp'"
          :alt="article.title"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        />
      </div>

      <div class="p-6 flex-grow">
        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
          {{ article.title }}
        </h3>

        <p class="text-gray-600 mb-4 line-clamp-3">
          {{ article.excerpt }}
        </p>

        <div class="flex items-center mb-3">
          <span class="text-gray-500 text-sm ml-2">{{ article.date }}</span>
        </div>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup>
defineProps({
  article: {
    type: Object,
    required: true,
  },
})
</script>
