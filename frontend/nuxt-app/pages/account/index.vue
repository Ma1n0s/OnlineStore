<template>
  <div class="space-y-6">
    <UserCard />

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium mb-4">Последние активности</h2>
      <div class="space-y-4">
        <div v-for="activity in activities" :key="activity.id" class="flex items-start">
          <div class="flex-shrink-0 bg-blue-100 p-2 rounded-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-blue-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
            <p class="text-sm text-gray-500">{{ activity.date }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
useHead({
  title: "Личный кабинет | Абсолют техно",
  meta: [
    {
      name: "description",
      content:
        "Личный кабинет Абсолют техно. Управление заказами, избранными товарами, просмотренными товарами и другими функциями.",
    },
  ],
});

const activities = [
  { id: 1, title: "Начислено 150 бонусов за покупку", date: "Сегодня, 14:30" },
  { id: 2, title: "Списано 50 бонусов за скидку", date: "Вчера, 11:45" },
  { id: 3, title: "Обновлены данные профиля", date: "2 дня назад" },
];
</script>
