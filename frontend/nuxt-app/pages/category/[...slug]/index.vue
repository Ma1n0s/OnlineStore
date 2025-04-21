<script setup>
const route = useRoute()
// const router = useRouter()
const { slug } = route.params
console.log(slug)

const { data: category } = await useFetch(() => `http://127.0.0.1:8000/api/categories/${slug.at(-1)}`)
// const { data: childCategories } = await useFetch(() => `/api/categories/${category.value.id}/children`)

// if (!category.value) {
//   throw createError({ statusCode: 404, message: 'Категория не найдена' })
// }

onMounted(async () => {
  // Проверяем есть ли дочерние категории
  if (!category.value.children || category.value.children.length === 0) {
    // Если нет, перенаправляем на страницу товаров
    await navigateTo(`/products/category/${route.params.slug.join('/')}`)
  }

  // console.log(childCategories.value)
  console.log(category.value)
})

useHead({
  title: `${route.params.category} | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты для строительства и ремота, категория ${route.params.category}`,
    },
  ],
})

import CategoryDescription from '~/components/CategoryItems/CategoryDescription/CategoryDescription.vue'
import CategoryList from '~/components/CategoryItems/CategoryList/CategoryList.vue'

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

  products: [
    {
      name: 'Шуруповерты',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Дрели',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Перфораторы',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Болгарки',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Пилы',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Фрезеры',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Шлифмашины',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Лобзики',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Строительные пылесосы',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Измерительные инструменты',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Краскопульты',
      image: 'Categories/Instruments.png',
    },
    {
      name: 'Тепловые пушки',
      image: 'Categories/Instruments.png',
    },
  ],
})
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="#" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Категория</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Подкатегория</NuxtLink>
    </nav>

    <div>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Строительный инструмент</h1>
        </div>
        <NuxtLink to="#" class="text-primary hover:underline mt-2 md:mt-0">Как выбрать электроинструмент</NuxtLink>
      </div>

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
