<script setup>
import { reactive, computed, onMounted } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'

const authStore = useAuthStore()
const uiState = reactive({
  isModalOpen: false,
  isCompanyModalOpen: false,
  isEditingCompany: false,
  showCompanyDetails: false,
  isLoading: false,
  error: null,
})

const profile = reactive({
  lastname: '',
  firstname: '',
  middlename: '',
  company: '',
  companyDetails: null,
  email: '',
  phone: '',
  registrationDate: '',
})

const forms = reactive({
  password: {
    current: '',
    new: '',
    confirm: '',
    errors: {},
  },
  company: {
    name: '',
    inn: '',
    kpp: '',
    address: '',
    director: '',
    phone: '',
    email: '',
    errors: {},
  },
})

const fullName = computed(() => `${profile.lastname} ${profile.firstname} ${profile.middlename}`)
const formattedRegistrationDate = computed(() => new Date(profile.registrationDate).toLocaleDateString('ru-RU'))
const hasCompanyDetails = computed(() => Boolean(profile.companyDetails?.inn))

const loadProfile = async () => {
  uiState.isLoading = true
  try {
    const response = await $fetch('/api/profile', {
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
    })
    Object.assign(profile, response.profile)
  } catch (error) {
    uiState.error = error.message
  } finally {
    uiState.isLoading = false
  }
}

const saveProfile = async () => {
  uiState.isLoading = true
  try {
    await $fetch('/api/profile', {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
      body: {
        lastname: profile.lastname,
        firstname: profile.firstname,
        middlename: profile.middlename,
        email: profile.email,
        phone: profile.phone,
      },
    })
    await loadProfile()
  } catch (error) {
    uiState.error = error.message
  } finally {
    uiState.isLoading = false
  }
}

const saveCompany = async () => {
  if (!validateCompany()) return
  uiState.isLoading = true
  try {
    await $fetch('/api/profile/company', {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
      body: forms.company,
    })
    await loadProfile()
    uiState.isCompanyModalOpen = false
  } catch (error) {
    uiState.error = error.message
  } finally {
    uiState.isLoading = false
  }
}

const changePassword = async () => {
  if (!validatePassword()) return
  uiState.isLoading = true
  try {
    await $fetch('/api/profile/password', {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
      body: {
        current_password: forms.password.current,
        new_password: forms.password.new,
      },
    })
    uiState.isModalOpen = false
    resetPasswordForm()
  } catch (error) {
    uiState.error = error.message
  } finally {
    uiState.isLoading = false
  }
}

onMounted(() => {
  loadProfile()
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
                  <TextInput type="text" id="firstname" v-model="profile.firstname" class="" />
                </div>
                <div>
                  <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                  <input
                    type="text"
                    id="lastname"
                    v-model="profile.lastname"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>
              </div>

              <div>
                <label for="middlename" class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
                <input
                  type="text"
                  id="middlename"
                  v-model="profile.middlename"
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
                      v-model="profile.company"
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
                  </TransitionExpand>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="profile.email"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="profile.phone"
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
              'shadow-sm focus:ring-primary': !forms.password.errors.current,
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
              'shadow-sm focus:ring-primary': !forms.password.errors.new,
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
              'shadow-sm focus:ring-primary': !forms.password.errors.confirm,
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
              'shadow-sm focus:ring-primary': !forms.company.errors.name,
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
                'shadow-sm focus:ring-primary': !forms.company.errors.inn,
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
