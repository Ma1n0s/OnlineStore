<script setup>
import { computed, ref } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})

const userStore = useUserStore()

const page = ref(1)
const perPage = ref(1)
const sort = ref('newest')

const allTransactions = computed(() => {
  if (!userStore.user?.bonusTransactions) return []

  return userStore.user.bonusTransactions.map(t => ({
    id: t.id,
    date: t.date ? new Date(t.date) : new Date(),
    operation: t.operation || 'Не указано',
    amount: t.amount || 0,
    status: t.status || 'Не указан',
    originalDate: t.date,
  }))
})
const transactions = computed(() => {
  let sorted = [...allTransactions.value]

  if (sort.value === 'newest') {
    sorted.sort((a, b) => b.date - a.date)
  } else {
    sorted.sort((a, b) => a.date - b.date)
  }

  const start = (page.value - 1) * perPage.value
  const end = start + perPage.value
  return sorted.slice(start, end).map(t => ({
    ...t,
    date: t.originalDate ? new Date(t.originalDate).toLocaleDateString('ru-RU') : 'Нет даты',
  }))
})

// Баланс бонусов
const bonusBalance = computed(() => {
  return userStore.user?.bonusBalance || 0
})

// Метаданные пагинации
const pagination = computed(() => {
  const total = allTransactions.value.length
  const lastPage = Math.ceil(total / perPage.value)

  return {
    current_page: page.value,
    last_page: lastPage,
    per_page: perPage.value,
    total: total,
    has_more: page.value < lastPage,
  }
})

// Изменение страницы
const changePage = newPage => {
  if (newPage >= 1 && newPage <= pagination.value.last_page) {
    page.value = newPage
  }
}

// Изменение количества элементов на странице
// const changePerPage = (newPerPage) => {
//   perPage.value = newPerPage
//   page.value = 1 // Сброс на первую страницу при изменении размера страницы
// }

// Изменение сортировки
// const changeSort = (newSort) => {
//   sort.value = newSort
//   page.value = 1 // Сброс на первую страницу при изменении сортировки
// }
</script>

<template>
  <div class="min-h-screen py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu class="hidden lg:block" />
        <div class="flex-1 space-y-6">
          <div class="bg-white shadow-2xl rounded-lg p-6">
            <h2 class="text-lg font-medium mb-4">Бонусный баланс: {{ bonusBalance }}</h2>
          </div>

          <div class="bg-white shadow-2xl rounded-lg p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
              <h2 class="text-lg font-medium">История операций</h2>
            </div>

            <div v-if="allTransactions.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
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
                      {{ transaction.amount > 0 ? '+' : '' }}{{ transaction.amount }}
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

              <!-- Пагинация -->
              <div class="w-full p-4 flex justify-center items-center gap-2 flex-wrap">
                <button
                  @click="changePage(1)"
                  :disabled="pagination.current_page === 1"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  &laquo;
                </button>

                <button
                  @click="changePage(pagination.current_page - 1)"
                  :disabled="pagination.current_page === 1"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  &lsaquo;
                </button>

                <template v-if="pagination.current_page > 2">
                  <button
                    @click="changePage(1)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    1
                  </button>
                  <span v-if="pagination.current_page > 3" class="px-2">...</span>
                </template>

                <button
                  v-if="pagination.current_page > 1"
                  @click="changePage(pagination.current_page - 1)"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                >
                  {{ pagination.current_page - 1 }}
                </button>

                <button
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-primary text-white"
                >
                  {{ pagination.current_page }}
                </button>

                <button
                  v-if="pagination.current_page < pagination.last_page"
                  @click="changePage(pagination.current_page + 1)"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                >
                  {{ pagination.current_page + 1 }}
                </button>

                <template v-if="pagination.current_page < pagination.last_page - 1">
                  <span v-if="pagination.current_page < pagination.last_page - 2" class="px-2">...</span>
                  <button
                    @click="changePage(pagination.last_page)"
                    class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                  >
                    {{ pagination.last_page }}
                  </button>
                </template>

                <button
                  @click="changePage(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page || !pagination.has_more"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  &rsaquo;
                </button>

                <button
                  @click="changePage(pagination.last_page)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  &raquo;
                </button>
              </div>
            </div>

            <div v-else class="text-center py-4 text-gray-500">Нет данных о бонусных операциях</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
