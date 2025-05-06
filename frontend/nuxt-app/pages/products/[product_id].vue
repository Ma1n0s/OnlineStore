<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Product } from '~/types/product.types'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const route = useRoute()
const { product_id } = route.params

const { data: product } = await useAsyncData<Product>(
  `products-${product_id}`,
  () => $fetch(`${backendUrl}/api/products/slug/${product_id}`),
  { revalidate: 3600 }
)

if (!product.value) {
  navigateTo('/404')
}

console.log(product.value, 'products')

// Импорты компонентов
// import DescriptionBlock from '~/components/Product/DescriptionBlock.vue'
import SpecificationsBlock from '~/components/Product/SpecificationsBlock.vue'
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import ImageBlock from '~/components/Product/ImageBlock.vue'
import BasicDescriptionBlock from '~/components/Product/BasicDescriptionBlock.vue'
import BrandBlock from '~/components/Product/BrandBlock.vue'
import Basket from '~/components/Product/Basket.vue'
import ActionsPanel from '~/components/Product/ActionsPanel.vue'
// import InformationCart from '~/components/Product/InformationCart.vue'
// import Reviews from '~/components/Product/Reviews.vue'
import DescriptionBlock from '~/components/Product/DescriptionBlock.vue'
import { getBreadcrumbsFromCategoryPath } from '~/components/BreadCrumbs/helpers'

// Вкладки
const tabs = ref([
  { id: 'description', title: 'ОПИСАНИЕ И ХАРАКТЕРИСТИКИ' },
  // { id: "reviews", title: "ОТЗЫВЫ" },
  // { id: "questions", title: "ВОПРОСЫ И ОТВЕТЫ" },
])

const activeTab = ref('description')
const isFavorite = ref(false)
const loading = ref(true)

onMounted(() => {
  activeTab.value = 'description'
  isFavorite.value = false
  loading.value = false
})

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
  <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />

    <div v-if="!loading">
      <ActionsPanel :product="product" />
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Свой Свайпер по img -->
        <ImageBlock :product="product" />

        <div class="grid grid-cols-2 gap-4">
          <!-- Основные характеристики -->
          <DescriptionBlock :product="product" />
          <Basket :product="product" />
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
            <!-- Описание -->
            <BasicDescriptionBlock :product="product" />
            <!-- Техническое описание -->
            <SpecificationsBlock :product="product" />
            <!-- <InformationCart /> -->
          </div>

          <!-- Боковая панель с брендом -->
          <div class="sticky top-4 h-fit">
            <BrandBlock :product="product" />
          </div>
        </div>
      </div>
      <!-- <Reviews /> -->
    </div>

    <div v-else class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
  </div>
</template>
