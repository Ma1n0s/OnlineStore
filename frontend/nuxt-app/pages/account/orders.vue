<script setup>
import { computed } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})

const userStore = useUserStore()

const orders = computed(() => {
  return (
    userStore.user?.orders?.map(order => ({
      id: order.order_number,
      status: getStatusText(order.status),
      date: new Date(order.created_at).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      }),
      amount: new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        maximumFractionDigits: 0,
      }).format(order.total_amount),
      paid: order.is_paid,
      products:
        order.products?.map(p => ({
          id: p.id,
          name: p.name,
          image: p.image || '/images/placeholder-product.png',
        })) || [],
    })) || []
  )
})

function getStatusText(status) {
  const statuses = {
    created: 'Создана',
    pending: 'В обработке',
    processing: 'В процессе',
    completed: 'Завершен',
    cancelled: 'Отменен',
  }
  return statuses[status] || status
}
</script>

<template>
  <div class="min-h-screen py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col lg:flex-row gap-6">
        <SidebarMenu class="hidden lg:block" />

        <div class="flex-1 space-y-6">
          <div class="bg-white p-6 rounded-xl shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
              <h2 class="text-2xl font-semibold text-gray-800">Мои заказы</h2>
            </div>

            <div class="flex flex-wrap gap-2">
              <button
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
              >
                Статус оплаты
                <Icon name="material-symbols:keyboard-arrow-down-rounded" />
              </button>

              <button
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
              >
                Статус заказа
                <Icon name="material-symbols:keyboard-arrow-down-rounded" />
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
            class="bg-white p-6 rounded-xl shadow-2xl hover:shadow-lg transition-all duration-300"
          >
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
              <div>
                <h3 class="font-medium text-gray-800">Заказ {{ order.id }}</h3>
                <p class="text-sm text-gray-500">{{ order.date }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span :class="{ 'text-red-600': !order.paid, 'text-green-600': order.paid }" class="font-medium">
                  {{ order.paid ? 'Оплачен' : 'Не оплачен' }} {{ order.amount }}
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
                class="px-4 py-2 bg-white shadow-2xl hover:shadow-md text-gray-700 rounded-lg text-sm font-medium transition-all"
              >
                Повторить заказ
              </button>
              <button
                class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg text-sm font-medium transition-colors shadow-md hover:shadow-lg"
              >
                Подробнее о заказе
              </button>
            </div>
          </div>

          <div v-if="orders.length === 0" class="bg-white p-8 rounded-xl shadow-2xl text-center">
            <Icon name="" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">У вас пока нет заказов</h3>
            <p class="mt-1 text-gray-500">Здесь будут отображаться ваши заказы после оформления</p>
            <button
              class="mt-6 px-6 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg"
            >
              Начать покупки
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
