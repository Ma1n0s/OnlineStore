<template>
  <div
    class="box-content absolute mt-1 w-screen lg:w-full !left-0 lg:left-auto bg-white shadow-2xl p-0 lg:p-1 rounded-xl z-20"
  >
    <div v-if="categories.length === 0 && products.length === 0">
      <p class="p-4 text-xl">Ничего не найдено</p>
    </div>
    <NuxtLink
      v-for="item in categories"
      :key="`${item.id} - category search`"
      class="flex items-center p-1 py-1 cursor-pointer first:rounded-t-xl hover:bg-red-100"
      :to="formatCategorySlug(item)"
      @click="closeSearch"
    >
      <NuxtImg :src="item.image_url || 'no-photo.webp'" width="40" height="40" />
      <div class="p-1 pl-2">
        <div class="text-dark/90">{{ item.title }}</div>
        <div class="text-dark/50 text-sm" v-if="item.root !== item.name">{{ item.root }}</div>
      </div>
    </NuxtLink>
    <hr class="border-primary/50 my-1" />
    <NuxtLink
      v-for="item in products"
      :key="`${item.id} - product search`"
      :to="`/products/${item.slug}`"
      @click="closeSearch"
      class="flex items-center p-1 py-1 cursor-pointer last:rounded-b-xl hover:bg-red-100"
    >
      <NuxtImg :src="item.main_image || 'no-photo.webp'" width="60" height="60" />
      <div>
        <div class="p-1 pl-2">
          {{ item.name }}
        </div>
        <div class="p-1 pl-2">{{ item.price }} руб.</div>
      </div>
    </NuxtLink>
  </div>
</template>

<script setup lang="ts">
import { onClickOutside } from '@vueuse/core'

const { target } = defineProps<{
  target: HTMLElement
}>()

const emit = defineEmits(['close'])

const products = defineModel<[]>('products', { required: true })
const categories = defineModel<[]>('categories', { required: true })

const closeSearch = () => emit('close')

const formatCategorySlug = category => {
  if (category.haveChildren) {
    return `products/category/${category.path}`
  }

  return `/category/${category.path}`
}

onClickOutside(target, closeSearch)
</script>
