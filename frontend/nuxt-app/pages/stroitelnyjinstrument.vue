<script setup>
const state = reactive({
  popularTags: [
    "Пылесосы с розеткой для электроинструмента",
    "Дрели-шуруповерты для женщин",
    "Дрели-шуруповерты для мебели",
    "Противопожарное оборудование и Inventar",
    "Зеленые дрели-шуруповерты аккумуляторные",
    "Синие дрели-шуруповерты аккумуляторные",
    "Желтые дрели-шуруповерты аккумуляторные"
  ],

  articles: [
    {
      id: 1,
      title: "Советы по выбору электроинструмента",
      excerpt: "Хорошие результаты получать не просто. Различные ситуации требуют...",
      date: "05.09.2024"
    },
    {
      id: 2,
      title: "Электроинструмент - как правильно работать",
      excerpt: "Каждый, кто хоть раз держал в руке электроинструмент, непременно должен знать...",
      date: "05.09.2024"
    },
    {
      id: 3,
      title: "Электроинструмент нужен в каждом доме",
      excerpt: "Вряд ли найдутся люди, готовые поспорить с тем, что мелкий домашний ремонт...",
      date: "07.06.2023"
    },
    {
      id: 4,
      title: "Всегда ли работает традиционная схема выбора?",
      excerpt: "Все рекомендации по вопросу выбора электроинструмента начинаются с...",
      date: "07.06.2023"
    }
  ],

  products: [
    {
      name: "Шуруповерты",
      image: "/images/screwdrivers.png"
    },
    {
      name: "Дрели",
      image: "/images/drills.png"
    },
    {
      name: "Перфораторы",
      image: "/images/hammers.png"
    },
    {
      name: "Болгарки",
      image: "/images/grinders.png"
    },
    {
      name: "Пилы",
      image: "/images/saws.png"
    },
    {
      name: "Фрезеры",
      image: "/images/mills.png"
    },
    {
      name: "Шлифмашины",
      image: "/images/sanders.png"
    },
    {
      name: "Лобзики",
      image: "/images/jigsaws.png"
    },
    {
      name: "Строительные пылесосы",
      image: "/images/vacuums.png"
    },
    {
      name: "Измерительные инструменты",
      image: "/images/measuring.png"
    },
    {
      name: "Краскопульты",
      image: "/images/sprayers.png"
    },
    {
      name: "Тепловые пушки",
      image: "/images/heatguns.png"
    }
  ]
});
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <nav class="flex flex-wrap items-center gap-2 text-gray mb-4">
      <NuxtLink to="#" class="hover:underline">Главная</NuxtLink>
      <span>/</span>
      <NuxtLink to="#" class="font-semibold">Инструмент</NuxtLink>
    </nav>

    <div>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold">Строительный инструмент</h1>
        </div>
        <NuxtLink to="#" class="text-primary hover:underline mt-2 md:mt-0">Как выбрать электроинструмент</NuxtLink>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <NuxtLink 
          v-for="(product, index) in state.products"
          :key="index"
          to="#"
          class="bg-gray-100 flex flex-col justify-between rounded-lg p-4 h-full hover:bg-gray-300 transition duration-200 ease-in-out shadow-md"
        >
          <div class="font-medium">{{ product.name }}</div>
          <NuxtImg 
            :src="product.image" 
            :alt="product.name" 
            class="self-end h-10 w-10 mt-2 object-contain"
            width="40"
            height="40"
            loading="lazy"
            format="webp"
          />
        </NuxtLink>
      </div>

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