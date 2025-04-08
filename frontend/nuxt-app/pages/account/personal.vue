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

import { ref } from "vue";
import Modal from "~/components/Modal/Modal.vue";

const isModalOpen = ref(false);
const isCompanyModalOpen = ref(false);
const isEditingCompany = ref(false);
const showCompanyDetails = ref(false);

const passwordForm = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});
const passwordErrors = ref({});

const profile = ref({
  lastname: "Иванов",
  firstname: "Иван",
  middlename: "Иванович",
  company: 'ООО "ТехноПром"',
  companyDetails: {
    name: 'ООО "ТехноПром"',
    inn: "1234567890",
    kpp: "987654321",
    address: "г. Москва, ул. Ленина, д. 1",
    director: "Иванов Иван Иванович",
    phone: "+7 (999) 123-45-67",
    email: "info@technoprom.ru",
  },
  email: "ivanov@example.com",
  phone: "+7 (999) 123-45-67",
});

const companyForm = ref({
  name: "",
  inn: "",
  kpp: "",
  address: "",
  director: "",
  phone: "",
  email: "",
});

const toggleCompanyDetails = () => {
  showCompanyDetails.value = !showCompanyDetails.value;
};

const openModal = () => {
  isModalOpen.value = true;
  passwordForm.value = { currentPassword: "", newPassword: "", confirmPassword: "" };
  passwordErrors.value = {};
};

const closeModal = () => {
  isModalOpen.value = false;
};

const validatePassword = () => {
  let isValid = true;
  passwordErrors.value = {};

  if (!passwordForm.value.currentPassword) {
    passwordErrors.value.currentPassword = "Введите текущий пароль";
    isValid = false;
  }

  if (!passwordForm.value.newPassword) {
    passwordErrors.value.newPassword = "Введите новый пароль";
    isValid = false;
  } else if (passwordForm.value.newPassword.length < 8) {
    passwordErrors.value.newPassword = "Пароль должен содержать минимум 8 символов";
    isValid = false;
  }

  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    passwordErrors.value.confirmPassword = "Пароли не совпадают";
    isValid = false;
  }

  return isValid;
};

const handlePasswordChange = () => {
  if (validatePassword()) {
    console.log("Пароль изменен:", passwordForm.value);
    closeModal();
  }
};

const openCompanyModal = () => {
  isCompanyModalOpen.value = true;
};

const closeCompanyModal = () => {
  isCompanyModalOpen.value = false;
  isEditingCompany.value = false;
};

const handleCompanyConfirm = () => {
  if (companyForm.value.name) {
    profile.value.company = companyForm.value.name;
    profile.value.companyDetails = { ...companyForm.value };
  }
  closeCompanyModal();
};

const startEditCompany = () => {
  if (profile.value.companyDetails) {
    companyForm.value = { ...profile.value.companyDetails };
  }
  isEditingCompany.value = true;
  openCompanyModal();
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-64 flex-shrink-0">
          <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-6 p-2 bg-gray-50 rounded-lg">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-gray-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.5"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                />
              </svg>
              <span class="font-medium text-gray-700">Мой кабинет</span>
            </div>
            <nav class="space-y-2">
              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4H5z"
                  />
                </svg>
                Бонусы
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                  />
                </svg>
                История покупок
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                  />
                </svg>
                Личные данные
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                  />
                </svg>
                Мои организации
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
                Корзина
              </RouterLink>

              <RouterLink
                to="/account/favorites"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                  />
                </svg>
                Избранное
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>
                Просмотренные товары
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                  />
                </svg>
                Обратная связь
              </RouterLink>

              <RouterLink
                to="#"
                class="flex items-center gap-3 p-2 bg-blue-50 text-blue-600 font-medium rounded-lg transition-colors text-sm"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-blue-500"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                Настройки профиля
              </RouterLink>
            </nav>
          </div>
        </div>

        <div class="flex-1 space-y-6">
          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-start gap-4">
              <div class="relative flex-shrink-0">
                <div
                  class="w-14 h-14 rounded-full bg-gradient-to-tr from-blue-100 to-purple-200 flex items-center justify-center overflow-hidden"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7 text-purple-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                </div>
                <span class="absolute bottom-1 right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-baseline gap-2 flex-wrap">
                  <h1 class="text-2xl font-bold text-gray-900 truncate">
                    {{ profile.lastname }} {{ profile.firstname }} {{ profile.middlename }}
                  </h1>
                </div>

                <p class="text-gray-500 text-sm mt-1 flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1.5 opacity-70"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                  Зарегистрирован: <span class="font-medium ml-1">13.07.2023</span>
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Личная информация</h2>

            <form class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                  <input
                    type="text"
                    id="lastname"
                    v-model="profile.lastname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  />
                </div>
                <div>
                  <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                  <input
                    type="text"
                    id="firstname"
                    v-model="profile.firstname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  />
                </div>
              </div>

              <div>
                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
                <input
                  type="text"
                  id="middlename"
                  v-model="profile.middlename"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                />
              </div>

              <div>
                <div class="flex items-end gap-3">
                  <div class="flex-1">
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Компания</label>
                    <input
                      type="text"
                      id="company"
                      v-model="profile.company"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    />
                  </div>
                  <button
                    type="button"
                    @click="startEditCompany"
                    class="h-10 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
                  >
                    <Icon name="tabler:edit" class="w-5 h-5" />
                    {{ profile.companyDetails ? "Изменить" : "Добавить" }}
                  </button>
                </div>

                <div v-if="profile.companyDetails" class="mt-3">
                  <div
                    class="flex items-center justify-between bg-gray-50 p-3 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors"
                    @click="toggleCompanyDetails"
                  >
                    <span class="text-sm font-medium text-gray-700">Данные компании</span>
                    <button
                      @click.stop="toggleCompanyDetails"
                      class="text-gray-500 hover:text-gray-700 transition-colors"
                      :title="showCompanyDetails ? 'Скрыть детали' : 'Показать детали'"
                    >
                      <Icon
                        name="tabler:chevron-down"
                        class="w-5 h-5 transition-transform duration-200"
                        :class="{ 'transform rotate-180': showCompanyDetails }"
                      />
                    </button>
                  </div>
                  <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="transform opacity-0 -translate-y-2"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 -translate-y-2"
                  >
                    <div v-show="showCompanyDetails" class="bg-gray-50 p-4 rounded-b-lg border-t border-gray-200 mt-0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <p class="text-xs text-gray-500 mb-1">ИНН</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.inn }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">КПП</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.kpp }}</p>
                        </div>
                        <div class="md:col-span-2">
                          <p class="text-xs text-gray-500 mb-1">Юридический адрес</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.address }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">Директор</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.director }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">Контактный телефон</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.phone }}</p>
                        </div>
                        <div class="md:col-span-2">
                          <p class="text-xs text-gray-500 mb-1">Email компании</p>
                          <p class="text-sm font-medium text-gray-900">{{ profile.companyDetails.email }}</p>
                        </div>
                      </div>
                    </div>
                  </transition>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="profile.email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="profile.phone"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                  />
                </div>
              </div>

              <div class="pt-4 flex justify-end">
                <button
                  type="submit"
                  class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                >
                  Сохранить изменения
                </button>
              </div>
            </form>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="border-t pt-4">
              <h3 class="font-medium mb-4 text-gray-900">Безопасность</h3>
              <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                  <p class="text-sm font-medium text-gray-900 mb-1">Desktop Windows 10</p>
                  <p class="text-xs text-gray-600">123 - Текущий сеанс</p>
                </div>

                <div class="flex flex-wrap gap-4 pt-2">
                  <button
                    @click="openModal"
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-2 transition"
                  >
                    <Icon name="tabler:key" class="w-5 h-5" />
                    Сменить пароль
                  </button>

                  <button
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-2 transition"
                  >
                    <Icon name="tabler:arrow-down-left" class="w-5 h-5" />
                    Выйти
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal
      :isOpen="isModalOpen"
      @close="closeModal"
      @confirm="handlePasswordChange"
      title="Смена пароля"
      confirmText="Сохранить новый пароль"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Текущий пароль</label>
          <input
            type="password"
            v-model="passwordForm.currentPassword"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            :class="{ 'border-red-500 focus:ring-red-200': passwordErrors.currentPassword }"
          />
          <p v-if="passwordErrors.currentPassword" class="mt-1 text-sm text-red-600">
            {{ passwordErrors.currentPassword }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
          <input
            type="password"
            v-model="passwordForm.newPassword"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            :class="{ 'border-red-500 focus:ring-red-200': passwordErrors.newPassword }"
          />
          <p v-if="passwordErrors.newPassword" class="mt-1 text-sm text-red-600">
            {{ passwordErrors.newPassword }}
          </p>
          <p class="mt-1 text-xs text-gray-500">Пароль должен содержать минимум 8 символов</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Подтвердите новый пароль</label>
          <input
            type="password"
            v-model="passwordForm.confirmPassword"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            :class="{ 'border-red-500 focus:ring-red-200': passwordErrors.confirmPassword }"
          />
          <p v-if="passwordErrors.confirmPassword" class="mt-1 text-sm text-red-600">
            {{ passwordErrors.confirmPassword }}
          </p>
        </div>
      </div>
    </Modal>

    <Modal
      :isOpen="isCompanyModalOpen"
      @close="closeCompanyModal"
      @confirm="handleCompanyConfirm"
      :title="isEditingCompany ? 'Редактирование данных компании' : 'Добавление данных компании'"
      large
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Название компании</label>
          <input
            type="text"
            v-model="companyForm.name"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
            <input
              type="text"
              v-model="companyForm.inn"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
            <input
              type="text"
              v-model="companyForm.kpp"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Юридический адрес</label>
          <input
            type="text"
            v-model="companyForm.address"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Директор</label>
          <input
            type="text"
            v-model="companyForm.director"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
          />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Контактный телефон</label>
            <input
              type="tel"
              v-model="companyForm.phone"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email компании</label>
            <input
              type="email"
              v-model="companyForm.email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            />
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>
