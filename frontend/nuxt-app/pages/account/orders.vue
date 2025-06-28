<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: `Заказы | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})

const page = ref(1)
const perPage = ref(10)

const ordersData = reactive({
  data: [],
  loading: false,
  error: null,
  pagination: {
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  },
})

const {
  public: { backendUrl },
} = useRuntimeConfig()

const loadOrders = async () => {
  ordersData.loading = true
  ordersData.error = null

  try {
    const response = await $fetch(`${backendUrl}/api/orders/full`, {
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
      query: {
        page: page.value,
        per_page: perPage.value,
      },
    })

    if (response && response.data) {
      ordersData.data = response.data
      ordersData.pagination = {
        current_page: response.current_page,
        last_page: response.last_page,
        per_page: response.per_page,
        total: response.total,
      }
    }
  } catch (error) {
    console.error('Ошибка загрузки заказов:', error)
    ordersData.error = 'Не удалось загрузить данные заказов. Пожалуйста, попробуйте позже.'
  } finally {
    ordersData.loading = false
  }
}

onMounted(() => {
  loadOrders()
})

watch([page], () => {
  loadOrders()
})

const orders = computed(() => {
  return ordersData.data
    .filter(order => order.status !== 'created')
    .map(order => ({
      id: order.order_number || order.id,
      status: getStatusText(order.status),
      date: new Date(order.created_at).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      }),
      originalDate: order.created_at,
      amount: new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        maximumFractionDigits: 0,
      }).format(order.total_amount),
      numericAmount: order.total_amount,
      paid: order.is_paid,
      products:
        order.products?.map(p => ({
          id: p.id,
          name: p.name,
          image: p.main_image || '/images/placeholder-product.png',
        })) || [],
    }))
})

function getStatusText(status) {
  const statuses = {
    pending: 'В обработке',
    processing: 'В процессе',
    completed: 'Завершен',
    cancelled: 'Отменен',
  }
  return statuses[status] || status
}

const changePage = newPage => {
  if (newPage >= 1 && newPage <= ordersData.pagination.last_page) {
    page.value = newPage
  }
}

const isMobileMenuOpen = ref(false)
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div v-if="isMobileMenuOpen" class="fixed inset-0 z-20 lg:hidden">
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="isMobileMenuOpen = false"></div>
      <div class="relative h-full w-80 max-w-full bg-white shadow-xl">
        <SidebarMenu @close="isMobileMenuOpen = false" />
      </div>
    </div>

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
      <div class="flex flex-col lg:flex-row gap-6">
        <SidebarMenu class="hidden lg:block" />

        <div class="flex-1 space-y-6">
          <div v-if="ordersData.loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mx-auto"></div>
            <p class="mt-2 text-gray-600">Загрузка данных...</p>
          </div>
          <template v-else>
            <div class="bg-white shadow rounded-lg p-4 lg:p-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                <h2 class="text-lg font-medium">Мои заказы</h2>
                <div class="text-sm text-gray-500">Всего: {{ ordersData.pagination.total }}</div>
              </div>

              <div v-if="ordersData.error" class="bg-red-50 p-4 rounded-lg text-red-600 mb-4">
                {{ ordersData.error }}
              </div>

              <div v-if="orders.length > 0">
                <div class="hidden lg:block overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Номер
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Дата
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Сумма
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Товары
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="order in orders" :key="order.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ order.date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ order.amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="{
                              'bg-blue-100 text-blue-800': order.status === 'В обработке',
                              'bg-yellow-100 text-yellow-800': order.status === 'В процессе',
                              'bg-green-100 text-green-800': order.status === 'Завершен',
                              'bg-red-100 text-red-800': order.status === 'Отменен',
                            }"
                          >
                            {{ order.status }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex -space-x-2">
                            <NuxtImg
                              v-for="product in order.products"
                              :key="product.id"
                              :src="product.image || 'no-photo.webp'"
                              :alt="product.name"
                              width="32"
                              height="32"
                              class="w-8 h-8 rounded-full border-2 border-white object-cover"
                              loading="lazy"
                            />
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="lg:hidden space-y-4">
                  <div v-for="order in orders" :key="order.id" class="border-b border-gray-200 pb-4">
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="font-medium text-gray-900">Заказ #{{ order.id }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ order.date }}</p>
                      </div>
                      <span class="text-sm font-medium text-gray-900">
                        {{ order.amount }}
                      </span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-blue-100 text-blue-800': order.status === 'В обработке',
                          'bg-yellow-100 text-yellow-800': order.status === 'В процессе',
                          'bg-green-100 text-green-800': order.status === 'Завершен',
                          'bg-red-100 text-red-800': order.status === 'Отменен',
                        }"
                      >
                        {{ order.status }}
                      </span>
                    </div>
                    <div class="mt-3 flex -space-x-2">
                      <NuxtImg
                        v-for="product in order.products"
                        :key="product.id"
                        :src="product.image || 'no-photo.webp'"
                        :alt="product.name"
                        width="32"
                        height="32"
                        class="w-8 h-8 rounded-full border-2 border-white object-cover"
                        loading="lazy"
                      />
                    </div>
                  </div>
                </div>

                <div
                  v-if="ordersData.pagination.last_page > 1"
                  class="w-full mt-6 hidden lg:flex justify-center items-center gap-2"
                >
                  <button
                    @click="changePage(1)"
                    :disabled="ordersData.pagination.current_page === 1"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &laquo;
                  </button>

                  <button
                    @click="changePage(ordersData.pagination.current_page - 1)"
                    :disabled="ordersData.pagination.current_page === 1"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &lsaquo;
                  </button>

                  <template v-if="ordersData.pagination.current_page > 2">
                    <button
                      @click="changePage(1)"
                      class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                    >
                      1
                    </button>
                    <span v-if="ordersData.pagination.current_page > 3" class="px-2">...</span>
                  </template>

                  <button
                    v-if="ordersData.pagination.current_page > 1"
                    @click="changePage(ordersData.pagination.current_page - 1)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    {{ ordersData.pagination.current_page - 1 }}
                  </button>

                  <button
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-primary text-white"
                  >
                    {{ ordersData.pagination.current_page }}
                  </button>

                  <button
                    v-if="ordersData.pagination.current_page < ordersData.pagination.last_page"
                    @click="changePage(ordersData.pagination.current_page + 1)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    {{ ordersData.pagination.current_page + 1 }}
                  </button>

                  <template v-if="ordersData.pagination.current_page < ordersData.pagination.last_page - 1">
                    <span v-if="ordersData.pagination.current_page < ordersData.pagination.last_page - 2" class="px-2"
                      >...</span
                    >
                    <button
                      @click="changePage(ordersData.pagination.last_page)"
                      class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                    >
                      {{ ordersData.pagination.last_page }}
                    </button>
                  </template>

                  <button
                    @click="changePage(ordersData.pagination.current_page + 1)"
                    :disabled="ordersData.pagination.current_page === ordersData.pagination.last_page"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &rsaquo;
                  </button>

                  <button
                    @click="changePage(ordersData.pagination.last_page)"
                    :disabled="ordersData.pagination.current_page === ordersData.pagination.last_page"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &raquo;
                  </button>
                </div>

                <div
                  v-if="ordersData.pagination.last_page > 1"
                  class="lg:hidden flex items-center justify-between mt-6 px-4"
                >
                  <button
                    @click="changePage(ordersData.pagination.current_page - 1)"
                    :disabled="ordersData.pagination.current_page === 1"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Назад
                  </button>

                  <div class="text-sm text-gray-600">
                    Страница {{ ordersData.pagination.current_page }} из {{ ordersData.pagination.last_page }}
                  </div>

                  <button
                    @click="changePage(ordersData.pagination.current_page + 1)"
                    :disabled="ordersData.pagination.current_page === ordersData.pagination.last_page"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Вперед
                  </button>
                </div>
              </div>

              <div v-else class="text-center py-12">
                <Icon name="mdi:package-variant-closed" class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">У вас пока нет заказов</h3>
                <p class="mt-1 text-gray-500">Здесь будут отображаться ваши заказы после оформления</p>
                <NuxtLink
                  to="/"
                  class="mt-6 inline-flex px-6 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors"
                >
                  Начать покупки
                </NuxtLink>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
