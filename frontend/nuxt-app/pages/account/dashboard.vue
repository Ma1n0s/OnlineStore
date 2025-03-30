<script setup>
const firstRowCards = [
  { icon: '', alt: 'but1', title: 'Мои заказы', description: 'Пока нет заказов' },
  { icon: '', alt: 'but2', title: 'Промокоды', description: 'Покупать с выгодой' },
  { icon: '', alt: 'but1', title: 'Купленное', description: 'Заказать снова' },
  { icon: '', alt: 'but1', title: 'Просмотренное', description: 'Легко найти' }
]

const secondRowCards = [
  { icon: '', alt: 'favorites', title: 'Избранное', count: 0 },
  { icon: '', alt: 'comparison', title: 'Сравнение', count: 0 }
]
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <h2 class="font-semibold text-3xl mb-4 max-w-6xl mx-auto flex gap-8">Настройки профиля</h2>
    <div class="max-w-6xl mx-auto flex gap-8">
      <div class="w-1/4 flex-shrink-0">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
          <nav class="flex flex-col gap-2">
            <RouterLink 
              to="#" 
              class="bg-blue-50 text-gray-600 font-medium rounded transition-colors text-sm"
            >
              Мой кабинет
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Заказы
            </RouterLink>

            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Обратная связь
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Настройки профиля
            </RouterLink>
          </nav>
        </div>
      </div>

      <div class="flex-1">
  <section class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
    <header class="mb-6">
      <h2 class="text-3xl font-semibold text-gray-800">Мой кабинет</h2>
    </header>

    <div class="grid gap-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <article 
          v-for="(card, index) in firstRowCards" 
          :key="index"
          class="rounded-md border border-gray-200 p-4 hover:border-blue-500 hover:shadow-md transition-all duration-200"
        >
          <div class="flex flex-col h-full">
            <img :src="card.icon" :alt="card.alt" class="w-6 h-6 mb-3 text-blue-500"/>
            <h3 class="font-medium text-gray-800 mb-1">{{ card.title }}</h3>
            <p class="text-sm text-gray-500 mt-auto">{{ card.description }}</p>
          </div>
        </article>
      </div>

      <!-- Второй ряд карточек -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <article 
          v-for="(card, index) in secondRowCards" 
          :key="index"
          class="bg-white rounded-md border border-gray-200 p-4 hover:shadow-md transition-all duration-200"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <img :src="card.icon" :alt="card.alt" class="w-8 h-8"/>
              <p class="font-medium text-gray-800">{{ card.title }}</p>
            </div>
            <span class="text-sm text-gray-500">{{ card.count }} товаров</span>
          </div>
        </article>
      </div>
    </div>
  </section>
</div>
    </div>
  </div>
</template>