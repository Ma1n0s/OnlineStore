<script setup>
import { reactive } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

const orders = reactive([
  {
    id: '#12345',
    date: '15.03.2023',
    status: 'Доставлен',
    total: 25490,
    items: [
      { name: 'Смартфон Samsung Galaxy S23', price: 79990, quantity: 1, image: '' },
      { name: 'Чехол для Samsung Galaxy S23', price: 1990, quantity: 1, image: '' }
    ]
  },
  {
    id: '#12344',
    date: '10.03.2023',
    status: 'Отменен',
    total: 12990,
    items: [
      { name: 'Наушники Sony WH-1000XM4', price: 24990, quantity: 1, image: '' }
    ]
  },
  {
    id: '#12343',
    date: '05.03.2023',
    status: 'Доставлен',
    total: 45990,
    items: [
      { name: 'Ноутбук ASUS VivoBook 15', price: 45990, quantity: 1, image: '' }
    ]
  }
])

const statusClasses = reactive({
  'Доставлен': 'bg-green-100 text-green-800',
  'Отменен': 'bg-red-100 text-red-800',
  'В обработке': 'bg-yellow-100 text-yellow-800',
  'В пути': 'bg-blue-100 text-blue-800'
})
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu />
        <div class="flex-1 space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">История покупок</h1>
            
            <div class="space-y-6">
              <div v-for="order in orders" :key="order.id" class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                  <div class="flex items-center gap-4">
                    <span class="font-medium">Заказ {{ order.id }}</span>
                    <span class="text-sm text-gray-500">{{ order.date }}</span>
                  </div>
                  <div class="flex items-center gap-4">
                    <span class="text-lg font-semibold">{{ order.total.toLocaleString() }} ₽</span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium" :class="statusClasses[order.status]">
                      {{ order.status }}
                    </span>
                  </div>
                </div>
                
                <div class="divide-y divide-gray-200">
                  <div v-for="(item, index) in order.items" :key="index" class="p-6 flex flex-col sm:flex-row gap-4">
                    <div class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                      <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                      <h3 class="font-medium text-gray-900">{{ item.name }}</h3>
                      <p class="text-sm text-gray-500 mt-1">Количество: {{ item.quantity }}</p>
                    </div>
                    <div class="text-lg font-medium">
                      {{ (item.price * item.quantity).toLocaleString() }} ₽
                    </div>
                  </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                  <button class="text-primary hover:text-primary-hover font-medium text-sm">
                    Повторить заказ
                  </button>
                </div>
              </div>
            </div>
            
            <div class="mt-8 flex justify-center">
              <nav class="flex items-center gap-2">
                <button class="px-3 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50">
                  &laquo;
                </button>
                <button class="px-3 py-1 rounded border border-blue-500 bg-blue-50 text-blue-600">
                  1
                </button>
                <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                  2
                </button>
                <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                  3
                </button>
                <button class="px-3 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50">
                  &raquo;
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>