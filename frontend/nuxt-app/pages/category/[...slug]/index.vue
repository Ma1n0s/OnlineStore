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
