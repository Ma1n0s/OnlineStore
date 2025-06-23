<template>
  <div class="min-h-screen bg-slate-100 pb-16">
    <div class="bg-white shadow-sm">
      <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 py-6">
        <div class="flex items-center gap-4">
          <div class="bg-primary/10 p-3 rounded-full">
            <Icon name="mdi:account-circle" class="w-8 h-8 text-primary" />
          </div>
          <div>
            <h1 class="text-lg sm:text-xl font-semibold text-gray-900">{{ userStore.user?.name || 'Пользователь' }}</h1>
            <div class="flex items-center gap-2 mt-1">
              <Icon name="mdi:gift" class="w-5 h-5 text-yellow-500" />
              <span class="text-sm sm:text-base font-medium text-gray-700">
                {{ userStore.user?.bonusBalance || 0 }} бонусов
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 py-6 space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <NuxtLink
          to="/account"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:user" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">Личные данные</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>

        <NuxtLink
          to="/account/cart-checkout"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:basket" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">Корзина</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>

        <NuxtLink
          to="/account/bonus"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:gift" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">Бонусы</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>

        <NuxtLink
          to="/account/companies"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:home" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">Мои организации</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>

        <NuxtLink
          to="/account/orders"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all col-span-1 sm:col-span-2"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:clipboard-list" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">История заказов</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>

        <NuxtLink
          to="/account/feedback"
          class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all col-span-1 sm:col-span-2"
        >
          <div class="flex items-center gap-3">
            <Icon name="tabler:message" class="w-6 h-6 text-primary" />
            <span class="text-gray-800">Обратная связь</span>
          </div>
          <Icon name="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
        </NuxtLink>
      </div>
    </div>

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 py-6">
      <button
        @click="logout"
        class="w-full flex items-center justify-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-red-600"
      >
        <Icon name="mdi:logout" class="w-5 h-5" />
        <span>Выйти из аккаунта</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { useUserStore } from '~/stores/user'
const userStore = useUserStore()

definePageMeta({
  middleware: ['auth'],
})

const logout = async () => {
  try {
    await $fetch('/api/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    })
    userStore.clearUser()
    navigateTo('/')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>

<style scoped>
.router-link-active {
  @apply bg-primary/5 border-l-4 border-primary;
}
</style>
