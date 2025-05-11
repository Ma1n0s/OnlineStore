<script setup>
import { ref, reactive } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

const uiState = reactive({
  isEditing: false,
  isLoading: false,
  error: null,
})

const company = ref({
  name: 'ООО "ТехноПром"',
  inn: '1234567890',
  kpp: '987654321',
  address: 'г. Москва, ул. Ленина, д. 1',
  director: 'Иванов Иван Иванович',
  phone: '+7 (999) 123-45-67',
  email: 'info@technoprom.ru',
})

const form = reactive({
  name: '',
  inn: '',
  kpp: '',
  address: '',
  director: '',
  phone: '',
  email: '',
  errors: {},
})

const startEditing = () => {
  Object.assign(form, company.value)
  uiState.isEditing = true
}

const cancelEditing = () => {
  uiState.isEditing = false
}

const validate = () => {
  let isValid = true
  form.errors = {}

  if (!form.name.trim()) {
    form.errors.name = 'Введите название компании'
    isValid = false
  }

  if (!form.inn.trim()) {
    form.errors.inn = 'Введите ИНН компании'
    isValid = false
  } else if (!/^\d{10,12}$/.test(form.inn)) {
    form.errors.inn = 'ИНН должен содержать 10 или 12 цифр'
    isValid = false
  }

  return isValid
}

const saveCompany = async () => {
  if (!validate()) return

  uiState.isLoading = true

  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
    Object.assign(company.value, form)
    uiState.isEditing = false
  } catch (error) {
    uiState.error = 'Ошибка при сохранении' + error.message
  } finally {
    uiState.isLoading = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu />

        <div class="flex-1 space-y-6">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Моя организация</h1>
            <button
              v-if="!uiState.isEditing"
              @click="startEditing"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2"
            >
              <Icon name="mdi:pencil" class="w-5 h-5" />
              Редактировать
            </button>
          </div>

          <div v-if="!uiState.isEditing" class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Основная информация</h3>
                  <div class="space-y-4">
                    <div>
                      <p class="text-sm text-gray-500">Название</p>
                      <p class="text-gray-900 font-medium">{{ company.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">ИНН</p>
                      <p class="text-gray-900 font-medium">{{ company.inn }}</p>
                    </div>
                    <div v-if="company.kpp">
                      <p class="text-sm text-gray-500">КПП</p>
                      <p class="text-gray-900 font-medium">{{ company.kpp }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Контактная информация</h3>
                  <div class="space-y-4">
                    <div>
                      <p class="text-sm text-gray-500">Юридический адрес</p>
                      <p class="text-gray-900 font-medium">{{ company.address }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Директор</p>
                      <p class="text-gray-900 font-medium">{{ company.director }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Телефон</p>
                      <p class="text-gray-900 font-medium">{{ company.phone }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Email</p>
                      <p class="text-gray-900 font-medium">{{ company.email }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
              <form @submit.prevent="saveCompany" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Основная информация</h3>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                        <input
                          v-model="form.name"
                          :class="{ 'border-red-300': form.errors.name }"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ИНН *</label>
                        <input
                          v-model="form.inn"
                          :class="{ 'border-red-300': form.errors.inn }"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="form.errors.inn" class="mt-1 text-sm text-red-600">{{ form.errors.inn }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
                        <input
                          v-model="form.kpp"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                    </div>
                  </div>

                  <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Контактная информация</h3>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Юридический адрес</label>
                        <input
                          v-model="form.address"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Директор</label>
                        <input
                          v-model="form.director"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                        <input
                          v-model="form.phone"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                          v-model="form.email"
                          type="email"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                  <button
                    type="button"
                    @click="cancelEditing"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                  >
                    Отмена
                  </button>
                  <button
                    type="submit"
                    :disabled="uiState.isLoading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-70"
                  >
                    <span v-if="uiState.isLoading" class="flex items-center">
                      <Icon name="mdi:loading" class="animate-spin mr-2" />
                      Сохранение...
                    </span>
                    <span v-else>Сохранить</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
