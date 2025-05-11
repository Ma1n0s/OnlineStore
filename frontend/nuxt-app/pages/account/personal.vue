<script setup>
import { ref, reactive, computed, watch } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'

const user = ref({
  firstname: '',
  lastname: '',
  middlename: '',
  email: '',
  phone: '',
  company: '',
  inn: '',
  kpp: '',
  address: '',
  director: '',
  company_phone: '',
  company_email: '',
  created_at: ''
})

const loading = ref(false)
const error = ref(null)
const successMessage = ref(null)

const uiState = reactive({
  isModalOpen: false,
  isCompanyModalOpen: false,
  isEditingCompany: false,
  showCompanyDetails: false
})

const forms = reactive({
  password: {
    current: '',
    new: '',
    confirm: '',
    errors: {}
  }
})

// Получаем данные пользователя
const fetchUser = async () => {
  try {
    loading.value = true
    const { data } = await useFetch('/api/profile', {
      headers: useRequestHeaders(['cookie'])
    })
    user.value = data.value.user
  } catch (err) {
    error.value = err.data?.message || 'Ошибка загрузки данных'
  } finally {
    loading.value = false
  }
}

// Обновление профиля
const updateProfile = async () => {
  try {
    loading.value = true
    await $fetch('/api/profile', {
      method: 'PUT',
      body: {
        firstname: user.value.firstname,
        lastname: user.value.lastname,
        middlename: user.value.middlename,
        email: user.value.email,
        phone: user.value.phone
      },
      headers: useRequestHeaders(['cookie'])
    })
    successMessage.value = 'Данные успешно сохранены'
    error.value = null
    setTimeout(() => successMessage.value = null, 3000)
  } catch (err) {
    error.value = err.data?.message || 'Ошибка обновления данных'
  } finally {
    loading.value = false
  }
}

// Обновление компании
const updateCompany = async () => {
  try {
    loading.value = true
    await $fetch('/api/profile/company', {
      method: 'PUT',
      body: {
        company: user.value.company,
        inn: user.value.inn,
        kpp: user.value.kpp,
        address: user.value.address,
        director: user.value.director,
        company_phone: user.value.company_phone,
        company_email: user.value.company_email
      },
      headers: useRequestHeaders(['cookie'])
    })
    successMessage.value = 'Данные компании успешно сохранены'
    error.value = null
    uiState.isCompanyModalOpen = false
    setTimeout(() => successMessage.value = null, 3000)
  } catch (err) {
    error.value = err.data?.message || 'Ошибка обновления данных компании'
  } finally {
    loading.value = false
  }
}

// Обновление пароля
const updatePassword = async () => {
  try {
    loading.value = true
    await $fetch('/api/profile/password', {
      method: 'PUT',
      body: {
        current_password: forms.password.current,
        password: forms.password.new,
        password_confirmation: forms.password.confirm
      },
      headers: useRequestHeaders(['cookie'])
    })
    successMessage.value = 'Пароль успешно изменен'
    error.value = null
    uiState.isModalOpen = false
    resetPasswordForm()
    setTimeout(() => successMessage.value = null, 3000)
  } catch (err) {
    error.value = err.data?.message || 'Ошибка смены пароля'
  } finally {
    loading.value = false
  }
}

const resetPasswordForm = () => {
  forms.password = {
    current: '',
    new: '',
    confirm: '',
    errors: {}
  }
}

const openPasswordModal = () => {
  uiState.isModalOpen = true
  resetPasswordForm()
}

const validatePassword = () => {
  let isValid = true
  forms.password.errors = {}

  if (!forms.password.current) {
    forms.password.errors.current = 'Введите текущий пароль'
    isValid = false
  }

  if (!forms.password.new) {
    forms.password.errors.new = 'Введите новый пароль'
    isValid = false
  } else if (forms.password.new.length < 8) {
    forms.password.errors.new = 'Пароль должен содержать минимум 8 символов'
    isValid = false
  }

  if (forms.password.new !== forms.password.confirm) {
    forms.password.errors.confirm = 'Пароли не совпадают'
    isValid = false
  }

  return isValid
}

const changePassword = async () => {
  if (!validatePassword()) return
  await updatePassword()
}

const prepareCompanyForm = () => {
  uiState.isEditingCompany = true
}

const openCompanyModal = () => {
  prepareCompanyForm()
  uiState.isCompanyModalOpen = true
}

const fullName = computed(() => `${user.value.lastname} ${user.value.firstname} ${user.value.middlename}`)
const formattedRegistrationDate = computed(() => new Date(user.value.created_at).toLocaleDateString('ru-RU'))
const hasCompanyDetails = computed(() => Boolean(user.value.inn))

onMounted(() => {
  fetchUser()
})
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div v-if="uiState.error" class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg shadow">
        {{ uiState.error }}
        <button @click="uiState.error = null" class="float-right font-bold">×</button>
      </div>

      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu />

        <div class="flex-1 space-y-6">
          <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex items-start gap-4">
              <div class="relative flex-shrink-0">
                <div
                  class="w-14 h-14 rounded-full bg-gradient-to-tr from-blue-100 to-purple-200 flex items-center justify-center overflow-hidden shadow"
                >
                  <Icon name="mdi:account" class="h-7 w-7 text-purple-600" />
                </div>
                <span class="absolute bottom-1 right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-baseline gap-2 flex-wrap">
                  <h1 class="text-2xl font-bold text-gray-900 truncate">
                    {{ fullName }}
                  </h1>
                </div>

                <p class="text-gray-500 text-sm mt-1 flex items-center">
                  <Icon name="mdi:calendar" class="h-4 w-4 mr-1.5 opacity-70" />
                  Зарегистрирован: <span class="font-medium ml-1">{{ formattedRegistrationDate }}</span>
                </p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Личная информация</h2>

            <form @submit.prevent="saveProfile" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                  <TextInput
                    type="text"
                    id="firstname"
                    v-model="user.firstname"
                    class=""
                  />
                </div>
                <div>
                  <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                  <input
                    type="text"
                    id="lastname"
                    v-model="user.lastname"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>
              
              </div>

              <div>
                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
                <input
                  type="text"
                  id="middlename"
                  v-model="user.middlename"
                  class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                />
              </div>

              <div>
                <div class="flex items-end gap-3">
                  <div class="flex-1">
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Компания</label>
                    <input
                      type="text"
                      id="company"
                      v-model="user.company"
                      class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                    />
                  </div>
                  <button
                    type="button"
                    @click="openCompanyModal(true)"
                    class="h-10 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm"
                  >
                    <Icon name="mdi:pencil" class="w-5 h-5" />
                    {{ hasCompanyDetails ? 'Изменить' : 'Добавить' }}
                  </button>
                </div>

                <div v-if="hasCompanyDetails" class="mt-3">
                  <div
                    class="flex items-center justify-between bg-gray-50 p-3 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors shadow-sm"
                    @click="toggleCompanyDetails"
                  >
                    <span class="text-sm font-medium text-gray-700">Данные компании</span>
                    <Icon
                      name="mdi:chevron-down"
                      class="w-5 h-5 transition-transform duration-200"
                      :class="{ 'transform rotate-180': uiState.showCompanyDetails }"
                    />
                  </div>
                  
                  <TransitionExpand>
                    <div v-if="uiState.showCompanyDetails" class="bg-gray-50 p-4 rounded-b-lg shadow-sm mt-0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <p class="text-xs text-gray-500 mb-1">ИНН</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.inn }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">КПП</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.kpp }}</p>
                        </div>
                        <div class="md:col-span-2">
                          <p class="text-xs text-gray-500 mb-1">Юридический адрес</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.address }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">Директор</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.director }}</p>
                        </div>
                        <div>
                          <p class="text-xs text-gray-500 mb-1">Контактный телефон</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.phone }}</p>
                        </div>
                        <div class="md:col-span-2">
                          <p class="text-xs text-gray-500 mb-1">Email компании</p>
                          <p class="text-sm font-medium text-gray-900">{{ user.companyDetails.email }}</p>
                        </div>
                      </div>
                    </div>
                  </TransitionExpand>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="user.email"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="user.phone"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>
              </div>

              <div class="pt-4 flex justify-end">
                <button
                  type="submit"
                  :disabled="uiState.isLoading"
                  class="inline-flex justify-center py-2.5 px-6 shadow-sm text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition disabled:opacity-70 disabled:cursor-not-allowed"
                >
                  <span v-if="uiState.isLoading" class="flex items-center">
                    <Icon name="mdi:loading" class="animate-spin mr-2" />
                    Сохранение...
                  </span>
                  <span v-else>Сохранить изменения</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <Modal
      :isOpen="uiState.isModalOpen"
      @close="uiState.isModalOpen = false"
      @confirm="changePassword"
      title="Смена пароля"
      confirmText="Сохранить новый пароль"
      :isLoading="uiState.isLoading"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Текущий пароль</label>
          <input
            type="password"
            v-model="forms.password.current"
            :class="{
              'shadow-red-100 focus:ring-red-200': forms.password.errors.current,
              'shadow-sm focus:ring-primary': !forms.password.errors.current
            }"
            class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:ring-opacity-50 transition shadow-sm"
          />
          <p v-if="forms.password.errors.current" class="mt-1 text-sm text-red-600">
            {{ forms.password.errors.current }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
          <input
            type="password"
            v-model="forms.password.new"
            :class="{
              'shadow-red-100 focus:ring-red-200': forms.password.errors.new,
              'shadow-sm focus:ring-primary': !forms.password.errors.new
            }"
            class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:ring-opacity-50 transition shadow-sm"
          />
          <p v-if="forms.password.errors.new" class="mt-1 text-sm text-red-600">
            {{ forms.password.errors.new }}
          </p>
          <p class="mt-1 text-xs text-gray-500">Пароль должен содержать минимум 8 символов</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Подтвердите новый пароль</label>
          <input
            type="password"
            v-model="forms.password.confirm"
            :class="{
              'shadow-red-100 focus:ring-red-200': forms.password.errors.confirm,
              'shadow-sm focus:ring-primary': !forms.password.errors.confirm
            }"
            class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:ring-opacity-50 transition shadow-sm"
          />
          <p v-if="forms.password.errors.confirm" class="mt-1 text-sm text-red-600">
            {{ forms.password.errors.confirm }}
          </p>
        </div>
      </div>
    </Modal>

    <Modal
      :isOpen="uiState.isCompanyModalOpen"
      @close="uiState.isCompanyModalOpen = false"
      @confirm="saveCompany"
      :title="uiState.isEditingCompany ? 'Редактирование данных компании' : 'Добавление данных компании'"
      large
      :isLoading="uiState.isLoading"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Название компании</label>
          <input
            type="text"
            v-model="forms.company.name"
            :class="{
              'shadow-red-100 focus:ring-red-200': forms.company.errors.name,
              'shadow-sm focus:ring-primary': !forms.company.errors.name
            }"
            class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:ring-opacity-50 transition shadow-sm"
          />
          <p v-if="forms.company.errors.name" class="mt-1 text-sm text-red-600">
            {{ forms.company.errors.name }}
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
            <input
              type="text"
              v-model="forms.company.inn"
              :class="{
                'shadow-red-100 focus:ring-red-200': forms.company.errors.inn,
                'shadow-sm focus:ring-primary': !forms.company.errors.inn
              }"
              class="w-full px-4 py-2 rounded-lg focus:ring-2 focus:ring-opacity-50 transition shadow-sm"
            />
            <p v-if="forms.company.errors.inn" class="mt-1 text-sm text-red-600">
              {{ forms.company.errors.inn }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
            <input
              type="text"
              v-model="forms.company.kpp"
              class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Юридический адрес</label>
          <input
            type="text"
            v-model="forms.company.address"
            class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Директор</label>
          <input
            type="text"
            v-model="forms.company.director"
            class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
          />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Контактный телефон</label>
            <input
              type="tel"
              v-model="forms.company.phone"
              class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email компании</label>
            <input
              type="email"
              v-model="forms.company.email"
              class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
            />
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>
<style>
.transition-expand-enter-active,
.transition-expand-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.transition-expand-enter-from,
.transition-expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.transition-expand-enter-to,
.transition-expand-leave-from {
  max-height: 500px;
  opacity: 1;
}
</style>