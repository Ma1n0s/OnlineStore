<script setup lang="ts">
const {
  public: { backendUrl },
} = useRuntimeConfig()

const { data: advantages, refresh } = await useAsyncData('admin-advantages', () => 
  $fetch(`${backendUrl}/api/advantages`)
)

const deleteAdvantage = async (id: number) => {
  await $fetch(`${backendUrl}/api/advantages/${id}`, {
    method: 'DELETE'
  })
  refresh()
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold">Управление преимуществами</h1>
      <NuxtLink 
        to="/admin/advantages/create"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
      >
        Добавить преимущество
      </NuxtLink>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Заголовок</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="advantage in advantages" :key="advantage.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ advantage.title }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="advantage.is_special ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'">
                {{ advantage.is_special ? 'Специальный' : 'Обычный' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <NuxtLink 
                :to="`/admin/advantages/${advantage.id}`"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Редактировать
              </NuxtLink>
              <button 
                @click="deleteAdvantage(advantage.id)"
                class="text-red-600 hover:text-red-900"
              >
                Удалить
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>