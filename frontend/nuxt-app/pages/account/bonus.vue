<script setup>
import { computed } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
const userStore = useUserStore()

const transactions = computed(() => {
  if (!userStore.user?.bonusTransactions) return []
  
  return userStore.user.bonusTransactions.map(t => ({
    id: t.id,
    date: t.date ? new Date(t.date).toLocaleDateString('ru-RU') : 'Нет даты',
    operation: t.operation || 'Не указано',
    amount: t.amount || 0,
    status: t.status || 'Не указан',
  }))
})

const bonusBalance = computed(() => {
  return userStore.user?.bonusBalance || 0
})
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu />
        <div class="flex-1 space-y-6">
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium mb-4">Бонусный баланс: {{ bonusBalance }}</h2>
          </div>
          
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium mb-4">История операций</h2>
            <div v-if="transactions.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Операция</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="transaction in transactions" :key="transaction.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ transaction.operation }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" 
                        :class="transaction.amount > 0 ? 'text-green-600' : 'text-red-600'">
                      {{ transaction.amount > 0 ? '+' : '' }}{{ transaction.amount }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="transaction.status === 'Завершено' 
                                   ? 'bg-green-100 text-green-800' 
                                   : 'bg-yellow-100 text-yellow-800'">
                        {{ transaction.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-4 text-gray-500">
              Нет данных о бонусных операциях
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>