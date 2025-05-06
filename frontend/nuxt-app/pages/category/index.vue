<script setup>
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: category } = await useAsyncData(`category-list`, () => $fetch(`${backendUrl}/api/categories`), {
  revalidate: 3600,
})

// getCachedData(key) {
//       return useNuxtApp().payload.data[key] // Использование кешированных данных
//     }

console.log(category.value, 'category')

useHead({
  title: `Категории | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Категории товаров, у нас на сайте вы найдете все для строительства и ремонта`,
    },
  ],
})

const breadcrumbs = [
  {
    name: 'Категории',
    url: '/category',
  },
]
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />

    <div>
      <CategoryDescription :data="category" />

      <CategoryList :categories="category" class="mb-8" />
    </div>
  </div>
</template>
