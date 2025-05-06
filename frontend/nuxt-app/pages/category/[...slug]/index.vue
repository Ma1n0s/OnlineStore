<script setup>
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import { getBreadcrumbs } from '~/components/BreadCrumbs/helpers'
import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const route = useRoute()
const { slug } = route.params

const { data: category } = await useAsyncData(
  `category-${slug}`,
  () => $fetch(`${backendUrl}/api/categories/slug/${slug.at(-1)}`),
  { revalidate: 3600 }
)
console.log(category.value)

if (!category.value?.children?.length) {
  // Если нет, перенаправляем на страницу товаров
  await navigateTo(`/products/category/${route.params.slug.join('/')}`)
}

onMounted(async () => {
  if (!category.value?.children?.length) {
    await navigateTo(`/products/category/${route.params.slug.join('/')}`)
  }
})

useHead({
  title: `${slug.at(-1)} | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты для строительства и ремота, категория ${slug.at(-1)}`,
    },
  ],
})

const breadcrumbs = [
  {
    name: 'Категории',
    url: '/category',
  },
  ...getBreadcrumbs(slug),
]
</script>

<template>
  <div class="mx-auto w-full max-w-screen-2xl px-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />
    <div>
      <CategoryDescription :data="category" />
      <CategoryList :categories="category.children" :path-prefix="`/category/${slug.join('/')}/`" class="mb-8" />
    </div>
  </div>
</template>
