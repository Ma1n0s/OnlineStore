<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-4">
    <NuxtLink
      v-for="(category, index) in categories"
      :to="getPath(category, pathPrefix)"
      :key="index + category.name"
      :alt="`Ссылка на категорию ${category.name}`"
      class="bg-gray/10 border h-fit sm:h-64 overflow-hidden cursor-pointer border-dark/20 hover:bg-gray/20 rounded-2xl shadow-xl hover:shadow-2xl"
    >
      <div class="relative h-32 sm:h-48 w-full">
        <NuxtImg
          :alt="category.title || category.name"
          :src="category?.image_url || 'no-photo.webp'"
          class="absolute h-full w-full object-cover"
          format="webp"
        />
      </div>
      <div class="flex items-center justify-start">
        <div class="flex items-center justify-center bg-primary text-white">
          <Icon name="material-symbols:navigate-before" class="w-8 h-8" />
        </div>
        <div
          class="w-full box-content h-14 p-1 pb-4 ~text-sm/xs md:text-lg text-dark font-medium text-center flex items-center justify-center"
        >
          {{ category.title || category.name }}
        </div>
      </div>
    </NuxtLink>
  </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import { getPath } from './CategoryList'

interface Category {
  id: number
  name: string
  title?: string
  image_url?: string
  slug?: string
}

const { categories, pathPrefix } = defineProps({
  categories: {
    type: Array as PropType<Category[]>,
    required: true,
  },
  pathPrefix: {
    type: String,
    default: '/category/',
  },
})
</script>
