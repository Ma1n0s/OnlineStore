<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

const userStore = useUserStore()
const uiState = reactive({
  isEditing: false,
  isLoading: false,
  error: null,
  isFirstAdd: false,
})

const company = ref({
  name: '',
  inn: '',
  kpp: '',
  address: '',
  director: '',
  phone: '',
  email: '',
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

const companySuggestions = ref([])
const selectedCompany = ref(null)
const innError = ref('')
const isLoadingSuggestions = ref(false)

// Вычисляемое свойство для валидности ИНН
const isInnValid = computed(() => {
  return form.inn && !innError.value && (form.inn.length === 10 || form.inn.length === 12)
})

onMounted(async () => {
  await loadCompanyData()
})

const loadCompanyData = async () => {
  uiState.isLoading = true
  try {
    await userStore.fetchUser()
    
    if (userStore.user?.profile) {
      company.value = {
        name: userStore.user.profile.company_name || '',
        inn: userStore.user.profile.inn || '',
        kpp: userStore.user.profile.kpp || '',
        address: userStore.user.profile.legal_address || '',
        director: userStore.user.profile.director || '',
        phone: userStore.user.profile.company_phone || '',
        email: userStore.user.profile.company_email || '',
      }
    }
  } catch (error) {
    uiState.error = 'Ошибка при загрузке данных компании: ' + error.message
  } finally {
    uiState.isLoading = false
  }
}

const startEditing = () => {
  Object.assign(form, company.value)
  uiState.isEditing = true
  
  if (!company.value.inn) {
    uiState.isFirstAdd = true
  }
}

const cancelEditing = () => {
  uiState.isEditing = false
  uiState.isFirstAdd = false
  companySuggestions.value = []
  selectedCompany.value = null
  innError.value = ''
}

const validateInn = async () => {
  if (!form.inn) {
    innError.value = 'Поле обязательно для заполнения'
    return false
  }

  const innRegex = /^\d+$/
  if (!innRegex.test(form.inn)) {
    innError.value = 'ИНН должен содержать только цифры'
    return false
  }

  if (form.inn.length !== 10 && form.inn.length !== 12) {
    innError.value = 'ИНН должен содержать 10 или 12 цифр'
    return false
  }

  innError.value = ''
  await searchCompanyByINN()
  return true
}

const searchCompanyByINN = async () => {
  if (!isInnValid.value) return

  isLoadingSuggestions.value = true
  companySuggestions.value = []

  try {
    const { data } = await axios.get('https://www.tinkoff.ru/api/common/dadata/suggestions/api/4_1/rs/suggest/party', {
      params: {
        appName: 'company-pages',
        query: form.inn,
      },
      withCredentials: false,
    })

    if (data.suggestions?.length) {
      companySuggestions.value = data.suggestions
    } else {
      innError.value = 'Компания с таким ИНН не найдена'
    }
  } catch (err) {
    console.error('Ошибка при проверке ИНН:', err)
    innError.value = 'Ошибка при проверке ИНН'
  } finally {
    isLoadingSuggestions.value = false
  }
}

const selectCompanySuggestion = (suggestion) => {
  selectedCompany.value = suggestion
  form.name = suggestion.value || ''
  form.kpp = suggestion.data.kpp || ''
  form.address = suggestion.data.address?.unrestricted_value || ''
  form.director = suggestion.data.management?.name || ''
  companySuggestions.value = []
}

const resetCompanySelection = () => {
  selectedCompany.value = null
  companySuggestions.value = []
  form.name = ''
  form.kpp = ''
  form.address = ''
  form.director = ''
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
  } else if (!isInnValid.value) {
    form.errors.inn = innError.value || 'Некорректный ИНН'
    isValid = false
  }

  return isValid
}

watch(() => form.inn, (newVal) => {
  if (newVal.length >= 10 && uiState.isFirstAdd) {
    const delaySearch = setTimeout(() => {
      validateInn()
      clearTimeout(delaySearch)
    }, 800)
  }
})

const saveCompany = async () => {
  if (!validate()) return

  uiState.isLoading = true
  uiState.error = null

  try {
    const response = await $fetch('/api/profile/company', {
      method: 'PUT',
      body: {
        name: form.name,
        inn: form.inn,
        kpp: form.kpp,
        address: form.address,
        director: form.director,
        phone: form.phone,
        email: form.email,
      },
    })

    Object.assign(company.value, form)
    uiState.isEditing = false
    uiState.isFirstAdd = false
    companySuggestions.value = []
    selectedCompany.value = null
    
    await userStore.fetchUser()
  } catch (error) {
    uiState.error = 'Ошибка при сохранении: ' + (error.data?.message || error.message)
  } finally {
    uiState.isLoading = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div v-if="uiState.error" class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg shadow">
        {{ uiState.error }}
        <button @click="uiState.error = null" class="float-right font-bold">×</button>
      </div>

      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu />

        <div class="flex-1 space-y-6">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Моя организация</h1>
            <button
              v-if="!uiState.isEditing && company.inn"
              @click="startEditing"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2"
            >
              <Icon name="mdi:pencil" class="w-5 h-5" />
              Редактировать
            </button>
            <button
              v-if="!company.inn"
              @click="startEditing"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2"
            >
              <Icon name="mdi:plus" class="w-5 h-5" />
              Добавить данные компании
            </button>
          </div>

          <div v-if="!uiState.isEditing && company.inn" class="bg-white shadow rounded-lg overflow-hidden">
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

          <div v-if="!uiState.isEditing && !company.inn" class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 text-center py-12">
              <Icon name="mdi:office-building" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">Данные компании не добавлены</h3>
              <p class="text-gray-500 mb-6">Добавьте информацию о вашей компании для работы с документами</p>
              <button
                @click="startEditing"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2 mx-auto"
              >
                <Icon name="mdi:plus" class="w-5 h-5" />
                Добавить данные компании
              </button>
            </div>
          </div>

      <div v-if="uiState.isEditing" class="bg-white shadow rounded-lg overflow-hidden">
          <div class="p-6">
            <form @submit.prevent="saveCompany" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Основная информация</h3>
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">ИНН *</label>
                      <input
                        v-model="form.inn"
                        @blur="validateInn"
                        :class="{ 'border-red-300': form.errors.inn || innError }"
                        class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Введите ИНН компании"
                      />
                      <p v-if="form.errors.inn" class="mt-1 text-sm text-red-600">{{ form.errors.inn }}</p>
                      <p v-if="innError && !form.errors.inn" class="mt-1 text-sm text-red-600">{{ innError }}</p>
                      
                      <div v-if="isLoadingSuggestions" class="mt-2 flex items-center text-gray-500">
                        <Icon name="mdi:loading" class="animate-spin mr-2" />
                        Поиск компании...
                      </div>
                      
                      <div v-if="companySuggestions.length > 0" class="mt-2 border rounded-lg shadow-sm">
                        <div 
                          v-for="suggestion in companySuggestions" 
                          :key="suggestion.data.inn"
                          @click="selectCompanySuggestion(suggestion)"
                          class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                        >
                          <div class="font-medium">{{ suggestion.value }}</div>
                          <div class="text-sm text-gray-500">
                            ИНН: {{ suggestion.data.inn }}, 
                            КПП: {{ suggestion.data.kpp || 'не указан' }}
                          </div>
                          <div class="text-sm text-gray-500">{{ suggestion.data.address?.unrestricted_value }}</div>
                        </div>
                      </div>
                      
                      <button
                        v-if="selectedCompany"
                        @click="resetCompanySelection"
                        type="button"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800"
                      >
                        Сбросить выбор компании
                      </button>
                    </div>
                      
                      <div v-if="!uiState.isFirstAdd || (uiState.isFirstAdd && form.name)">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                        <input
                          v-model="form.name"
                          :class="{ 'border-red-300': form.errors.name }"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
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