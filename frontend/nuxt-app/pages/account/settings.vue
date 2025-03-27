<script setup>
import { ref } from "vue";
import Modal from "~/components/Modal/Modal.vue";


const isModalOpen = ref(false);
const isCompanyModalOpen = ref(false);
const isEditingCompany = ref(false);
const showCompanyDetails = ref(false);

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
});
const passwordErrors = ref({});

const profile = ref({
  lastname: 'Иванов',
  firstname: 'Иван',
  middlename: 'Иванович',
  company: 'ООО "ТехноПром"',
  companyDetails: {
    name: 'ООО "ТехноПром"',
    inn: '1234567890',
    kpp: '987654321',
    address: 'г. Москва, ул. Ленина, д. 1',
    director: 'Иванов Иван Иванович',
    phone: '+7 (999) 123-45-67',
    email: 'info@technoprom.ru'
  },
  email: 'ivanov@example.com',
  phone: '+7 (999) 123-45-67'
});

const companyForm = ref({
  name: '',
  inn: '',
  kpp: '',
  address: '',
  director: '',
  phone: '',
  email: ''
});

// Меню
const menuItems = ref([
  'Заказы',
  'Избранное',
  'Мои отзывы',
  'Мои адреса',
  'Бонусы: 0 Б',
  'Обратная связь',
  'Настройка уведомлений',
  'Настройки профиля'
]);

const toggleCompanyDetails = () => {
  showCompanyDetails.value = !showCompanyDetails.value;
};

const openModal = () => {
  isModalOpen.value = true;
  passwordForm.value = { currentPassword: '', newPassword: '', confirmPassword: '' };
  passwordErrors.value = {};
};

const closeModal = () => {
  isModalOpen.value = false;
};

const validatePassword = () => {
  let isValid = true;
  passwordErrors.value = {};

  if (!passwordForm.value.currentPassword) {
    passwordErrors.value.currentPassword = 'Введите текущий пароль';
    isValid = false;
  }

  if (!passwordForm.value.newPassword) {
    passwordErrors.value.newPassword = 'Введите новый пароль';
    isValid = false;
  } else if (passwordForm.value.newPassword.length < 8) {
    passwordErrors.value.newPassword = 'Пароль должен содержать минимум 8 символов';
    isValid = false;
  }

  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    passwordErrors.value.confirmPassword = 'Пароли не совпадают';
    isValid = false;
  }

  return isValid;
};

const handlePasswordChange = () => {
  if (validatePassword()) {
    console.log('Пароль изменен:', passwordForm.value);
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
  <div class="container mx-auto px-4 md:px-6 lg:px-8 py-8">
    <h2 class="font-semibold text-3xl mb-4">Настройки профиля</h2>
    <div class="max-w-6xl mx-auto flex gap-8">
      <div class="w-1/4 flex-shrink-0">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
          <nav class="flex flex-col gap-2">
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Заказы
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Избранное
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Мои отзывы
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Мои адреса
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Бонусы: 0 Б
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Обратная связь
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 hover:bg-gray-100 rounded transition-colors text-sm"
            >
              Настройка уведомлений
            </RouterLink>
            <RouterLink 
              to="#" 
              class="p-2 bg-blue-50 text-gray-600 font-medium rounded transition-colors text-sm"
            >
              Настройки профиля
            </RouterLink>
          </nav>
        </div>
      </div>

      <div class="flex-1">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
          <div class="flex items-start gap-4">
            <div class="relative flex-shrink-0">
              <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-50 to-purple-100 flex items-center justify-center overflow-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-baseline gap-2 flex-wrap">
                <h1 class="text-xl font-bold text-gray-900 truncate">Пришелец-HW05981</h1>
              </div>
              
              <p class="text-gray-500 text-sm mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Зарегистрирован: <span class="font-medium ml-1">13.07.2023</span>
              </p>
            </div>

            <button class="flex-shrink-0 text-purple-600 hover:text-purple-800 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
              </svg>
            </button>
          </div>

          <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4">
              <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Фамилия</label>
                    <input 
                      type="text" 
                      id="lastname" 
                      v-model="profile.lastname"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                  <div>
                    <label for="firstname" class="block text-sm font-medium text-gray-700">Имя</label>
                    <input 
                      type="text" 
                      id="firstname" 
                      v-model="profile.firstname"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                </div>
                
                <div>
                  <label for="middlename" class="block text-sm font-medium text-gray-700">Отчество</label>
                  <input 
                    type="text" 
                    id="middlename" 
                    v-model="profile.middlename"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                
                <div>
                  <div class="flex items-end gap-2">
                    <div class="flex-1">
                      <label for="company" class="block text-sm font-medium text-gray-700">Компания</label>
                      <input 
                        type="text" 
                        id="company" 
                        v-model="profile.company"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      >
                    </div>
                    <button 
                      type="button" 
                      @click="startEditCompany"
                      class="h-10 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium transition-colors"
                    >
                      {{ profile.companyDetails ? 'Изменить' : 'Добавить' }} данные
                    </button>
                  </div>
                  
                  <div v-if="profile.companyDetails" class="mt-2">
                    <div 
                      class="flex items-center justify-between bg-gray-50 p-2 rounded-t-lg cursor-pointer hover:bg-gray-100 transition-colors"
                      @click="toggleCompanyDetails"
                    >
                      <span class="text-xs font-medium text-gray-500">Данные компании</span>
                      <button 
                        @click.stop="toggleCompanyDetails"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                        :title="showCompanyDetails ? 'Скрыть детали' : 'Показать детали'"
                      >
                        <svg 
                          xmlns="http://www.w3.org/2000/svg" 
                          class="h-4 w-4 transition-transform duration-200" 
                          fill="none" 
                          viewBox="0 0 24 24" 
                          stroke="currentColor"
                          :class="{ 'transform rotate-180': !showCompanyDetails }"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
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
                      <div 
                        v-show="showCompanyDetails"
                        class="bg-gray-50 p-4 rounded-b-lg border-t border-gray-200"
                      >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <p class="text-xs text-gray-500">ИНН</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.inn }}</p>
                          </div>
                          <div>
                            <p class="text-xs text-gray-500">КПП</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.kpp }}</p>
                          </div>
                          <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Юридический адрес</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.address }}</p>
                          </div>
                          <div>
                            <p class="text-xs text-gray-500">Директор</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.director }}</p>
                          </div>
                          <div>
                            <p class="text-xs text-gray-500">Контактный телефон</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.phone }}</p>
                          </div>
                          <div class="md:col-span-2">
                            <p class="text-xs text-gray-500">Email компании</p>
                            <p class="text-sm font-medium">{{ profile.companyDetails.email }}</p>
                          </div>
                        </div>
                      </div>
                    </transition>
                  </div>
                </div>
                
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="profile.email"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
                  <input 
                    type="tel" 
                    id="phone" 
                    v-model="profile.phone"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
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
            
            <div class="border-t pt-4 flex flex-col gap-3">
              <!-- <button class="text-red-600 hover:text-red-700 text-xs self-start">
                Удалить профиль
              </button> -->
              <div class="flex gap-4">
                <button class="text-blue-600 hover:text-blue-700 text-xs">
                  Выйти
                </button>
                <button @click="openModal" class="text-blue-600 hover:text-blue-700 text-xs">
                  Сменить пароль
                </button>
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
                  <label class="block text-sm font-medium text-gray-700">Текущий пароль</label>
                  <input 
                    type="password" 
                    v-model="passwordForm.currentPassword"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': passwordErrors.currentPassword }"
                  >
                  <p v-if="passwordErrors.currentPassword" class="mt-1 text-sm text-red-600">
                    {{ passwordErrors.currentPassword }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Новый пароль</label>
                  <input 
                    type="password" 
                    v-model="passwordForm.newPassword"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': passwordErrors.newPassword }"
                  >
                  <p v-if="passwordErrors.newPassword" class="mt-1 text-sm text-red-600">
                    {{ passwordErrors.newPassword }}
                  </p>
                  <p class="mt-1 text-xs text-gray-500">
                    Пароль должен содержать минимум 8 символов
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Подтвердите новый пароль</label>
                  <input 
                    type="password" 
                    v-model="passwordForm.confirmPassword"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': passwordErrors.confirmPassword }"
                  >
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
                  <label class="block text-sm font-medium text-gray-700">Название компании</label>
                  <input 
                    type="text" 
                    v-model="companyForm.name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">ИНН</label>
                    <input 
                      type="text" 
                      v-model="companyForm.inn"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">КПП</label>
                    <input 
                      type="text" 
                      v-model="companyForm.kpp"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Юридический адрес</label>
                  <input 
                    type="text" 
                    v-model="companyForm.address"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Директор</label>
                  <input 
                    type="text" 
                    v-model="companyForm.director"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Контактный телефон</label>
                    <input 
                      type="tel" 
                      v-model="companyForm.phone"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Email компании</label>
                    <input 
                      type="email" 
                      v-model="companyForm.email"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                  </div>
                </div>
              </div>
            </Modal>
          </div>
        </div>
        
        <div class="bg-white mt-6 p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
          <div class="border-t pt-4">
            <h3 class="font-medium mb-4 text-sm">Ваши сеансы</h3>
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm">Desktop Windows 10</p>
              <p class="text-xs text-gray-600 mt-1">123 - Текущий сеанс</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>