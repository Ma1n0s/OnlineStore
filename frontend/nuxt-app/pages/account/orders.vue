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
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
            <div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-2xl font-bold mb-6">Мои заказы</h1>
    <div class="mb-6">
      <p class="text-gray-600 mb-4">Поиск по номеру заказа, названию товара или его коду</p>
      
      <div class="flex flex-wrap gap-4 mb-6">
        <button class="px-4 py-2 border rounded-lg flex items-center gap-2">
          <span>Статус оплаты</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        
        <button class="px-4 py-2 border rounded-lg flex items-center gap-2">
          <span>Статус заказа</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        
        <button class="px-4 py-2 border rounded-lg flex items-center gap-2">
          <span>Скрыть отмененные</span>
        </button>
      </div>
      
      <div class="border-b border-gray-200"></div>
    </div>
    
    <div class="border rounded-lg p-6 mb-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-lg font-semibold">Заказ 2503-211902-06296 <span class="text-gray-400">&gt;</span></h2>
          <p class="text-gray-500">от 3 марта</p>
        </div>
        
        <div class="text-right">
          <p class="text-red-500 font-medium">Не оплачен</p>
          <p class="text-lg font-semibold">6 734 ₽</p>
        </div>
      </div>
      
      <div class="flex flex-wrap gap-3 mt-6">
        <button class="px-4 py-2 border rounded-lg">Отказ от заказа</button>
        <button class="px-4 py-2 border rounded-lg">Повторить заказ</button>
        <button class="px-4 py-2 border rounded-lg">Подробнее о заказе</button>
      </div>
    </div>
  </div>    
        </div>
    </div>
    </div>
  </div>
</template>