<script setup>
import Breadcrumbs from '~/components/BreadCrumbs/Breadcrumbs.vue'
import { getBreadcrumbs } from '~/components/BreadCrumbs/helpers'
import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'

const route = useRoute()
const { slug } = route.params

const { data: category } = await useFetch(() => `http://127.0.0.1:8000/api/categories/slug/${slug.at(-1)}`)

if (!category.value.children || category.value.children.length === 0) {
  // Если нет, перенаправляем на страницу товаров
  await navigateTo(`/products/category/${route.params.slug.join('/')}`)
}

onMounted(async () => {
  if (!category.value.children || category.value.children.length === 0) {
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

const state = reactive({
  popularTags: [
    'Пылесосы с розеткой для электроинструмента',
    'Дрели-шуруповерты для женщин',
    'Дрели-шуруповерты для мебели',
    'Противопожарное оборудование и Inventar',
    'Зеленые дрели-шуруповерты аккумуляторные',
    'Синие дрели-шуруповерты аккумуляторные',
    'Желтые дрели-шуруповерты аккумуляторные',
  ],

  articles: [
    {
      id: 1,
      title: 'Советы по выбору электроинструмента',
      excerpt: 'Хорошие результаты получать не просто. Различные ситуации требуют...',
      date: '05.09.2024',
    },
    {
      id: 2,
      title: 'Электроинструмент - как правильно работать',
      excerpt: 'Каждый, кто хоть раз держал в руке электроинструмент, непременно должен знать...',
      date: '05.09.2024',
    },
    {
      id: 3,
      title: 'Электроинструмент нужен в каждом доме',
      excerpt: 'Вряд ли найдутся люди, готовые поспорить с тем, что мелкий домашний ремонт...',
      date: '07.06.2023',
    },
    {
      id: 4,
      title: 'Всегда ли работает традиционная схема выбора?',
      excerpt: 'Все рекомендации по вопросу выбора электроинструмента начинаются с...',
      date: '07.06.2023',
    },
  ],
})

const breadcrumbs = [
  {
    name: 'Категория',
    url: '/category',
  },
  ...getBreadcrumbs(slug),
]
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <Breadcrumbs :list="breadcrumbs" />
    <div>
      <CategoryDescription :data="category" />

      <CategoryList :categories="category.children" :path-prefix="`/category/${slug.join('/')}/`" class="mb-8" />

      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Часто ищут</h2>
        <div class="flex flex-wrap gap-2">
          <NuxtLink
            v-for="(tag, index) in state.popularTags"
            :key="index"
            to="#"
            class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
          >
            {{ tag }}
          </NuxtLink>
          <NuxtLink
            to="#"
            class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
          >
            ... Показать ещё
          </NuxtLink>
        </div>
      </div>

      <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Статьи</h2>
          <NuxtLink
            to="#"
            class="bg-gray-200 hover:bg-gray-300 rounded-md px-4 py-2 transition duration-200 ease-in-out"
          >
            Все статьи
          </NuxtLink>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <NuxtLink
            v-for="article in state.articles"
            :key="article.id"
            to="#"
            class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition duration-200 ease-in-out"
          >
            <h3 class="font-semibold text-lg mb-2">{{ article.title }}</h3>
            <p class="text-gray-600 mb-4">{{ article.excerpt }}</p>
            <p class="text-gray-400 text-sm">{{ article.date }}</p>
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>
