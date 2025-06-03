<template>
  <div class="w-full md:w-64 flex-shrink-0 h-full">
    <div class="bg-white p-5 rounded-xl shadow-2xl h-full flex flex-col">
      <nav class="space-y-2 flex-1">
        <NuxtLink
          to="/account/personal"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:user" class="w-6 h-6" />
          Личные данные
        </NuxtLink>

        <NuxtLink
          to="/account/cart-checkout"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:basket" class="w-6 h-6" />
          Корзина
        </NuxtLink>

        <NuxtLink
          to="/account/bonus"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:gift" class="w-6 h-6" />
          Бонусы
        </NuxtLink>

        <NuxtLink
          to="/account/companies"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:home" class="w-6 h-6" />
          Мои организации
        </NuxtLink>

        <NuxtLink
          to="/account/orders"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:clipboard-list" class="w-6 h-6" />
          История заказов
        </NuxtLink>

        <NuxtLink
          to="/account/feedback"
          class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm hover:shadow-sm"
          active-class="bg-gray-100 text-primary font-medium"
        >
          <Icon name="tabler:message" class="w-6 h-6" />
          Обратная связь
        </NuxtLink>
      </nav>
    </div>
  </div>
</template>
