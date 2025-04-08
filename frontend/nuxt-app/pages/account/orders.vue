<script setup>
useHead({
  title: "Мои заказы | Абсолют техно",
  meta: [
    {
      name: "description",
      content:
        "История заказов в Абсолют техно. Просматривайте все ваши заказы, отслеживайте их статус и детали. Удобный доступ к информации о доставке, оплате и составе заказов.",
    },
  ],
});

const firstRowCards = [
  { icon: "", alt: "but1", title: "Мои заказы", description: "Пока нет заказов" },
  { icon: "", alt: "but2", title: "Промокоды", description: "Покупать с выгодой" },
  { icon: "", alt: "but1", title: "Купленное", description: "Заказать снова" },
  { icon: "", alt: "but1", title: "Просмотренное", description: "Легко найти" },
];

const secondRowCards = [
  { icon: "", alt: "favorites", title: "Избранное", count: 0 },
  { icon: "", alt: "comparison", title: "Сравнение", count: 0 },
];

const orders = [
  {
    id: "2503-211902-06296",
    status: "Отказ от заказа",
    date: "3 марта 2024",
    amount: "6 734 ₽",
    paid: false,
    products: [
      { id: 1, name: "Товар 1", image: "Categories/Instruments.png" },
      { id: 2, name: "Товар 2", image: "Categories/Instruments.png" },
    ],
  },
];
</script>

<template>
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <h2 class="font-semibold text-3xl mb-6 max-w-6xl mx-auto">Настройки профиля</h2>
    <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-6">
      <div class="w-full lg:w-1/4 flex-shrink-0">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 sticky top-6">
          <div class="flex items-center gap-3 mb-6 p-2 bg-gray-50 rounded-lg">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-gray-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
              />
            </svg>
            <span class="font-medium text-gray-700">Мой кабинет</span>
          </div>
          <nav class="space-y-2">
            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4H5z"
                />
              </svg>
              Бонусы
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
              История покупок
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
              Личные данные
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                />
              </svg>
              Мои организации
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
              Корзина
            </NuxtLink>

            <NuxtLink
              to="/account/favorites"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
              Избранное
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
              </svg>
              Просмотренные товары
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                />
              </svg>
              Обратная связь
            </NuxtLink>

            <NuxtLink
              to="#"
              class="flex items-center gap-3 p-2 bg-blue-50 text-blue-600 font-medium rounded-lg transition-colors text-sm"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-blue-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
              Настройки профиля
            </NuxtLink>
          </nav>
        </div>
      </div>

      <div class="flex-1 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Мои заказы</h2>

            <div class="relative w-full md:w-64">
              <input
                type="search"
                placeholder="Поиск по заказам..."
                class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <Icon name="material-symbols:search-rounded" class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
            >
              Статус оплаты
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <button
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
            >
              Статус заказа
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <button
              class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors"
            >
              Скрыть отмененные
            </button>
          </div>
        </div>

        <div
          v-for="order in orders"
          :key="order.id"
          class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300"
        >
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
            <div>
              <h3 class="font-medium text-gray-800">Заказ {{ order.id }}</h3>
              <p class="text-sm text-gray-500">{{ order.date }}</p>
            </div>
            <div class="flex items-center gap-2">
              <span :class="{ 'text-red-600': !order.paid, 'text-green-600': order.paid }" class="font-medium">
                {{ order.paid ? "Оплачен" : "Не оплачен" }} {{ order.amount }}
              </span>
            </div>
          </div>

          <hr class="my-4 border-gray-200" />

          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium self-start">
              {{ order.status }}
            </span>

            <div class="flex -space-x-2">
              <NuxtImg
                v-for="product in order.products"
                :key="product.id"
                :src="product.image"
                :alt="product.name"
                class="w-10 h-10 rounded-full border-2 border-white object-cover"
              />
            </div>
          </div>

          <div class="flex flex-wrap gap-3 pt-4">
            <button
              class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg text-sm font-medium transition-colors"
            >
              Повторить заказ
            </button>
            <button
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors"
            >
              Подробнее о заказе
            </button>
          </div>
        </div>

        <div v-if="orders.length === 0" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-12 w-12 mx-auto text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
            />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">У вас пока нет заказов</h3>
          <p class="mt-1 text-gray-500">Здесь будут отображаться ваши заказы после оформления</p>
          <button
            class="mt-6 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
          >
            Начать покупки
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
