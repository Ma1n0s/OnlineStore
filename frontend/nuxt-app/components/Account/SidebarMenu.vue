<template>
  <div class="w-full md:w-64 flex-shrink-0">
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
      <div class="flex items-center gap-3 mb-6 p-2 bg-gray-50 rounded-lg">
        <Icon name="tabler:user-circle" class="w-6 h-6" />
        <span class="font-medium text-gray-700">Мой кабинет</span>
      </div>
      <nav class="space-y-2">
        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:gift" class="w-6 h-6" />
          Бонусы
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:clipboard-list" class="w-6 h-6" />
          История покупок
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:user" class="w-6 h-6" />
          Личные данные
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:home" class="w-6 h-6" />
          Мои организации
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:basket" class="w-6 h-6" />
          Корзина
        </NuxtLink>

        <NuxtLink
          to="/account/favorites"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:heart" class="w-6 h-6" />
          Избранное
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:eye" class="w-6 h-6" />
          Просмотренные товары
        </NuxtLink>

        <NuxtLink
          to="#"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
        >
          <Icon name="tabler:message" class="w-6 h-6" />
          Обратная связь
        </NuxtLink>
      </nav>
    </div>
  </div>
</template>
