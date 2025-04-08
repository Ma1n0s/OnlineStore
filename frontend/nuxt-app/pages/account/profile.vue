<script setup>
useHead({
  title: "Личные данные | Абсолют техно",
  meta: [
    {
      name: "description",
      content:
        "Личные данные в личном кабинете Абсолют техно. Управление данными, просмотр и редактирование личных данных.",
    },
  ],
});

const profile = ref({
  lastname: "Иванов",
  firstname: "Иван",
  middlename: "Иванович",
  company: 'ООО "ТехноПром"',
  email: "ivanov@example.com",
  phone: "+7 (999) 123-45-67",
});
</script>
<template>
  <div class="space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium mb-6">Личные данные</h2>

      <form class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="lastname" class="block text-sm font-medium text-gray-700">Фамилия</label>
            <input
              type="text"
              id="lastname"
              v-model="profile.lastname"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label for="firstname" class="block text-sm font-medium text-gray-700">Имя</label>
            <input
              type="text"
              id="firstname"
              v-model="profile.firstname"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <div>
          <label for="middlename" class="block text-sm font-medium text-gray-700">Отчество</label>
          <input
            type="text"
            id="middlename"
            v-model="profile.middlename"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="company" class="block text-sm font-medium text-gray-700">Компания</label>
          <input
            type="text"
            id="company"
            v-model="profile.company"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            v-model="profile.email"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
          <input
            type="tel"
            id="phone"
            v-model="profile.phone"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div class="pt-4">
          <button
            type="submit"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Сохранить изменения
          </button>
        </div>
      </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium mb-4">Смена пароля</h2>
      <form class="space-y-4">
        <div>
          <label for="current-password" class="block text-sm font-medium text-gray-700">Текущий пароль</label>
          <input
            type="password"
            id="current-password"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="new-password" class="block text-sm font-medium text-gray-700">Новый пароль</label>
          <input
            type="password"
            id="new-password"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="confirm-password" class="block text-sm font-medium text-gray-700">Подтвердите пароль</label>
          <input
            type="password"
            id="confirm-password"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div class="pt-2">
          <button
            type="submit"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Сменить пароль
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
