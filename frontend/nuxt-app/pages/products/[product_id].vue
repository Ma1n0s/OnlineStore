<script setup lang="ts">
// Импорты компонентов
import SpecificationsBlock from '~/components/Product/SpecificationsBlock.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import ImageBlock from '~/components/Product/ImageBlock.vue'
import BasicDescriptionBlock from '~/components/Product/BasicDescriptionBlock.vue'
import BrandBlock from '~/components/Product/BrandBlock.vue'
import Basket from '~/components/Product/Basket.vue'
import ActionsPanel from '~/components/Product/ActionsPanel.vue'
import DescriptionBlock from '~/components/Product/DescriptionBlock.vue'
import { getBreadcrumbsFromCategoryPath } from '~/components/BreadCrumbs/helpers'

import { ref, onMounted } from 'vue'
import type { Product } from '~/types/product.types'

// const nuxtApp = useNuxtApp()

const {
  public: { backendUrl },
} = useRuntimeConfig()

const route = useRoute()
const { product_id } = route.params

// const { data: product } = await useFetch<Product>(`/api/products/slug/${product_id}`, {
//   baseURL: backendUrl,
//   key: `product-${product_id}`,
// })

// const { data: product, error } = await useAsyncData<Product>(
//   `products-${product_id}`,
//   async () => {
//     try {
//       return await $fetch(`${backendUrl}/api/products/slug/${product_id}`)
//     } catch (err) {
//       console.error('Ошибка загрузки продукта:', err)
//       throw createError({
//         statusCode: 500,
//         message: 'Не удалось загрузить данные продукта',
//       })
//     }
//   },
//   {
//     getCachedData(key) {
//       return useNuxtApp().payload.data[key]
//     },
//   }
// )

// const {
//   data: product,
//   pending,
//   error,
// } = await useFetch<Product>(`${backendUrl}/api/products/slug/${product_id}`, {
//   key: `product-${product_id}`,
//   getCachedData(key) {
//     // Используем кэшированные данные из payload или статического кэша
//     return nuxtApp.payload.data[key] || nuxtApp.static.data[key]
//   },
// })

// const { data: product } = await useFetch<Product>(`${backendUrl}/api/products/slug/${product_id}`, {
//   key: `product-${product_id}`,
//   getCachedData(key) {
//     return useNuxtApp().payload.data[key]
//   },
// })

const { data: product, refresh } = await useAsyncData<Product>(
  `product-${product_id}`,
  () => $fetch(`${backendUrl}/api/products/slug/${product_id}`),
  {
    transform: data => {
      if (!data) {
        throw createError({ statusCode: 404, statusMessage: 'Продукт не найден' })
      }
      return data
    },
  }
)

console.log(product.value)

useHead({
  title: `${product.value.name} | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты для строительства и ремота, ${product.value.description}`,
    },
  ],
})

// Вкладки
const tabs = ref([{ id: 'description', title: 'ОПИСАНИЕ И ХАРАКТЕРИСТИКИ' }])

const activeTab = ref('description')
const isFavorite = ref(false)
const loading = ref(true)

onMounted(async () => {
  activeTab.value = 'description'
  isFavorite.value = false
  loading.value = false

  console.log('mounted')

  await refresh()

  // if (process.client && product_id && !product.value) {
  //   await refresh()
  // }

  if (!product.value) {
    navigateTo('/404')
  }
})

// onUnmounted(() => {
//   delete nuxtApp.payload.data[`products-${product_id}`]
// })

const breadcrumbs = [
  {
    name: 'Категории',
    url: '/category',
  },
  ...getBreadcrumbsFromCategoryPath(product.value?.category),
  {
    name: product.value?.name || 'Продукт',
    url: `/products/${product_id}`,
  },
]
</script>

<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-8 space-y-6 sm:space-y-8 py-4 sm:py-8">
    <Breadcrumbs :list="breadcrumbs" class="px-2 sm:px-0" />

    <div v-if="!loading">
      <div class="flex flex-col lg:flex-row gap-4 sm:gap-8">
        <ImageBlock :product="product" class="w-full lg:w-2/5" />

        <div class="lg:flex-1 flex flex-col">
          <ActionsPanel :product="product" />

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mt-4 flex-grow">
            <div class="lg:col-span-2 lg:pr-24">
              <DescriptionBlock :product="product" />
            </div>
            <div class="lg:col-span-1">
              <Basket :product="product" />
            </div>
          </div>
        </div>
      </div>

      <div class="border-b border-gray-200 mb-6 sm:mb-8 overflow-x-auto">
        <nav class="flex space-x-6 min-w-max px-4 sm:px-0">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="py-3 sm:py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
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
        <div v-show="activeTab === 'description'" class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">
          <div class="lg:col-span-3">
            <BasicDescriptionBlock :product="product" />
            <SpecificationsBlock :product="product" />
          </div>

          <div class="sticky top-4 h-fit lg:block hidden">
            <BrandBlock :product="product" />
          </div>
        </div>
      </div>
    </div>

    <div v-else class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
  </div>
</template>
