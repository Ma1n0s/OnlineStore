<script setup>
useHead({
  title: 'Бонусы | Абсолют техно',
  meta: [
    {
      name: 'description',
      content:
        'Бонусная программа Абсолют техно. Узнайте о ваших накопленных бонусах, способах их получения и использования. Получайте скидки и специальные предложения за покупки.',
    },
  ],
})

const transactions = [
  { id: 1, date: '15.03.2023', operation: 'Начисление бонусов', amount: 150, status: 'Завершено' },
  { id: 2, date: '10.03.2023', operation: 'Списание бонусов', amount: -50, status: 'Завершено' },
  { id: 3, date: '05.03.2023', operation: 'Начисление бонусов', amount: 200, status: 'Завершено' },
  { id: 4, date: '28.02.2023', operation: 'Начисление бонусов', amount: 100, status: 'Завершено' },
  { id: 5, date: '25.02.2023', operation: 'Списание бонусов', amount: -75, status: 'Завершено' },
]
</script>
<template>
  <div class="space-y-6 mx-auto w-full max-w-screen-2xl px-8 space-y-16 py-8">
    <BonusCard />

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium mb-4">История операций</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Дата
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Операция
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Сумма
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Статус
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transaction in transactions" :key="transaction.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.date }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ transaction.operation }}</td>
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
                    transaction.status === 'Завершено' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  "
                >
                  {{ transaction.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
