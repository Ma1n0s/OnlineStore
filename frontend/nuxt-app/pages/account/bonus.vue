<script setup>
import { computed, ref, onMounted } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})

const page = ref(1)
const perPage = ref(10)

const bonusData = ref({
  balance: 0,
  transactions: [],
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

const loadBonusData = async () => {
  bonusData.value.loading = true
  bonusData.value.error = null

  try {
    const response = await $fetch(`${backendUrl}/api/bonus/transactions`, {
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
      bonusData.value = {
        balance: response.bonus_balance || 0,
        transactions: response.data.map(item => ({
          id: item.id,
          date: item.date || item.created_at,
          operation: item.operation || 'Начисление ',
          amount: item.amount || 0,
          status: item.status || 'Завершено',
          originalDate: item.date || item.created_at,
        })),
        pagination: response.meta || {
          current_page: 1,
          last_page: 1,
          per_page: perPage.value,
          total: 0,
        },
        loading: false,
        error: null,
      }
    }
  } catch (e) {
    console.error('Ошибка при загрузке бонусных данных:', e)
    bonusData.value.error = 'Не удалось загрузить данные'
    bonusData.value.loading = false
  }
}

onMounted(() => {
  loadBonusData()
})

watch([page], () => {
  loadBonusData()
})

const transactions = computed(() => {
  return bonusData.value.transactions.map(t => ({
    ...t,
    date: t.originalDate ? new Date(t.originalDate).toLocaleDateString('ru-RU') : 'Нет даты',
  }))
})

const bonusBalance = computed(() => {
  return bonusData.value.balance
})

const changePage = newPage => {
  if (newPage >= 1 && newPage <= bonusData.value.pagination.last_page) {
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
          <div v-if="bonusData.loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mx-auto"></div>
            <p class="mt-2 text-gray-600">Загрузка данных...</p>
          </div>
          <template v-else>
            <div class="bg-white shadow rounded-lg p-4 lg:p-6">
              <h2 class="text-lg font-medium">Бонусный баланс</h2>
              <p class="text-2xl font-bold mt-2">{{ bonusBalance.toFixed(2) }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-4 lg:p-6">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                <h2 class="text-lg font-medium">История операций</h2>
              </div>

              <div v-if="bonusData.transactions.length > 0">
                <div class="hidden lg:block overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Дата
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Операция
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Сумма
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Статус
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="transaction in transactions" :key="transaction.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ transaction.operation }}
                        </td>
                        <td
                          class="px-6 py-4 whitespace-nowrap text-sm"
                          :class="transaction.amount > 0 ? 'text-green-600' : 'text-red-600'"
                        >
                          {{ transaction.amount > 0 ? '+' : '' }}{{ transaction.amount.toFixed(2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="
                              transaction.status === 'Завершено'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-yellow-100 text-yellow-800'
                            "
                          >
                            {{ transaction.status }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="lg:hidden space-y-4">
                  <div v-for="transaction in transactions" :key="transaction.id" class="border-b border-gray-200 pb-4">
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="font-medium text-gray-900">{{ transaction.operation }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ transaction.date }}</p>
                      </div>
                      <span class="text-sm" :class="transaction.amount > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ transaction.amount > 0 ? '+' : '' }}{{ transaction.amount.toFixed(2) }}
                      </span>
                    </div>
                    <div class="mt-2">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="
                          transaction.status === 'Завершено'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-yellow-100 text-yellow-800'
                        "
                      >
                        {{ transaction.status }}
                      </span>
                    </div>
                  </div>
                </div>

                <div
                  v-if="bonusData.pagination.last_page > 1"
                  class="w-full mt-6 hidden lg:flex justify-center items-center gap-2"
                >
                  <button
                    @click="changePage(1)"
                    :disabled="bonusData.pagination.current_page === 1"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &laquo;
                  </button>

                  <button
                    @click="changePage(bonusData.pagination.current_page - 1)"
                    :disabled="bonusData.pagination.current_page === 1"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &lsaquo;
                  </button>

                  <template v-if="bonusData.pagination.current_page > 2">
                    <button
                      @click="changePage(1)"
                      class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                    >
                      1
                    </button>
                    <span v-if="bonusData.pagination.current_page > 3" class="px-2">...</span>
                  </template>

                  <button
                    v-if="bonusData.pagination.current_page > 1"
                    @click="changePage(bonusData.pagination.current_page - 1)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    {{ bonusData.pagination.current_page - 1 }}
                  </button>

                  <button
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-primary text-white"
                  >
                    {{ bonusData.pagination.current_page }}
                  </button>

                  <button
                    v-if="bonusData.pagination.current_page < bonusData.pagination.last_page"
                    @click="changePage(bonusData.pagination.current_page + 1)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    {{ bonusData.pagination.current_page + 1 }}
                  </button>

                  <template v-if="bonusData.pagination.current_page < bonusData.pagination.last_page - 1">
                    <span v-if="bonusData.pagination.current_page < bonusData.pagination.last_page - 2" class="px-2"
                      >...</span
                    >
                    <button
                      @click="changePage(bonusData.pagination.last_page)"
                      class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                    >
                      {{ bonusData.pagination.last_page }}
                    </button>
                  </template>

                  <button
                    @click="changePage(bonusData.pagination.current_page + 1)"
                    :disabled="bonusData.pagination.current_page === bonusData.pagination.last_page"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &rsaquo;
                  </button>

                  <button
                    @click="changePage(bonusData.pagination.last_page)"
                    :disabled="bonusData.pagination.current_page === bonusData.pagination.last_page"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    &raquo;
                  </button>
                </div>

                <div
                  v-if="bonusData.pagination.last_page > 1"
                  class="lg:hidden flex items-center justify-between mt-6 px-4"
                >
                  <button
                    @click="changePage(bonusData.pagination.current_page - 1)"
                    :disabled="bonusData.pagination.current_page === 1"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Назад
                  </button>

                  <div class="text-sm text-gray-600">
                    Страница {{ bonusData.pagination.current_page }} из {{ bonusData.pagination.last_page }}
                  </div>

                  <button
                    @click="changePage(bonusData.pagination.current_page + 1)"
                    :disabled="bonusData.pagination.current_page === bonusData.pagination.last_page"
                    class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Вперед
                  </button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
