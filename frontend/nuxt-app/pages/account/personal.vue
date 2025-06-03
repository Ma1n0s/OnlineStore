<script setup>
import { reactive, computed, onMounted } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
import Modal from '~/components/Modal/Modal.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const userStore = useUserStore()
const uiState = reactive({
  isModalOpen: false,
  isCompanyModalOpen: false,
  isEditingCompany: false,
  showCompanyDetails: false,
  isLoading: false,
  error: null,
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

const profile = computed(() => ({
  name: userStore.user?.name || '',
  email: userStore.user?.email || '',
  phone: userStore.user?.phone || '',
  registrationDate: userStore.user?.created_at || '',
  company: userStore.user?.company_name || '',
  companyDetails: userStore.user?.companyDetails || null,
}))

// const fullName = computed(() => `${profile.value.lastname} ${profile.value.firstname} ${profile.value.middlename}`)
// const formattedRegistrationDate = computed(() => new Date(profile.value.registrationDate).toLocaleDateString('ru-RU'))
const hasCompanyDetails = computed(() => Boolean(profile.value.companyDetails?.inn))

const validateCompany = () => {
  let isValid = true
  forms.company.errors = {}

  if (!forms.company.name.trim()) {
    forms.company.errors.name = 'Введите название компании'
    isValid = false
  }

  if (!forms.company.inn.trim()) {
    forms.company.errors.inn = 'Введите ИНН компании'
    isValid = false
  }

  return isValid
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
    forms.password.errors.new = 'Пароль должен содержать минимум 6 символов'
    isValid = false
  }

  if (forms.password.new !== forms.password.confirm) {
    forms.password.errors.confirm = 'Пароли не совпадают'
    isValid = false
  }

  return isValid
}

const resetPasswordForm = () => {
  forms.password = {
    current: '',
    new: '',
    confirm: '',
    errors: {},
  }
}

const openCompanyModal = (editMode = false) => {
  uiState.isEditingCompany = editMode

  forms.company = {
    name: '',
    inn: '',
    kpp: '',
    address: '',
    director: '',
    phone: '',
    email: '',
    errors: {},
  }

  if (profile.value.companyDetails) {
    forms.company.name = profile.value.companyDetails.name || ''
    forms.company.inn = profile.value.companyDetails.inn || ''
    forms.company.kpp = profile.value.companyDetails.kpp || ''
    forms.company.address = profile.value.companyDetails.address || ''
    forms.company.director = profile.value.companyDetails.director || ''
    forms.company.phone = profile.value.companyDetails.phone || ''
    forms.company.email = profile.value.companyDetails.email || ''
  } else if (profile.value.company) {
    forms.company.name = profile.value.company || ''
  }

  uiState.isCompanyModalOpen = true
}

const toggleCompanyDetails = () => {
  uiState.showCompanyDetails = !uiState.showCompanyDetails
}

const loadProfile = async () => {
  uiState.isLoading = true
  try {
    await userStore.fetchUser()
  } catch (error) {
    uiState.error = error.data?.message || error.message
  } finally {
    uiState.isLoading = false
  }
}

const saveProfile = async () => {
  uiState.isLoading = true
  try {
    const response = await $fetch(`${backendUrl}/api/profile`, {
      method: 'PUT',
      body: {
        name: profile.value.name,
        email: profile.value.email,
        phone: profile.value.phone,
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    userStore.setUser(response.user)
    uiState.error = null
  } catch (error) {
    uiState.error = error.data?.message || error.message || 'Ошибка при сохранении профиля'
    console.log(error)
  } finally {
    uiState.isLoading = false
  }
}

const saveCompany = async () => {
  if (!validateCompany()) return
  uiState.isLoading = true
  try {
    const response = await $fetch(`${backendUrl}/api/profile/company`, {
      method: 'PUT',
      body: forms.company,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    })

    console.log(response)

    await loadProfile()
    uiState.isCompanyModalOpen = false
  } catch (error) {
    uiState.error = error.data?.message || error.message
  } finally {
    uiState.isLoading = false
  }
}

const changePassword = async () => {
  if (!validatePassword()) return
  uiState.isLoading = true
  try {
    await $fetch(`${backendUrl}/api/profile/password`, {
      method: 'PUT',
      body: {
        current_password: forms.password.current,
        new_password: forms.password.new,
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
    })
    uiState.isModalOpen = false
    resetPasswordForm()
  } catch (error) {
    uiState.error = error.data?.message || error.message
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
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-2 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu class="hidden md:block" />

        <div class="flex-1 space-y-6">
          <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Личная информация</h2>

            <form class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">ФИО</label>
                  <input
                    type="text"
                    id="firstname"
                    v-model="profile.name"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>
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

                  <div>
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
                  </div>
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
                    maxlength="18"
                    placeholder="+7 (___) ___-__-__"
                    v-model="profile.phone"
                    class="w-full px-4 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition"
                  />
                </div>
              </div>

              <div class="pt-4 flex justify-between">
                <button
                  type="button"
                  @click="uiState.isModalOpen = true"
                  class="bg-primary text-white inline-flex justify-center py-2.5 px-6 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-primary-hover focus:outline-none focus:ring-2"
                >
                  Изменить пароль
                </button>

                <button
                  @click.prevent="saveProfile"
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
          <p class="mt-1 text-xs text-gray-500">Пароль должен содержать минимум 6 символов</p>
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
        <button
          @click.prevent="changePassword"
          :disabled="uiState.isLoading"
          class="inline-flex justify-center py-2.5 px-6 shadow-sm text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition disabled:opacity-70 disabled:cursor-not-allowed"
        >
          <span v-if="uiState.isLoading" class="flex items-center">
            <Icon name="mdi:loading" class="animate-spin mr-2" />
            Сохранение...
          </span>
          <span v-else>изменить пароль</span>
        </button>
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
