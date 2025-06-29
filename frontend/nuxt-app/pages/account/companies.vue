<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
import CheckBox from '~/components/Account/CheckBox.vue'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: `Компании | Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})

const {
  public: { backendUrl },
} = useRuntimeConfig()

const uiState = reactive({
  isEditing: false,
  isLoading: false,
  error: null,
  isFirstAdd: false,
  showFullForm: false,
  editingCompanyId: null,
})

const companies = ref([])
const mainCompany = ref(null)
const form = reactive({
  name: '',
  inn: '',
  kpp: '',
  address: '',
  director: '',
  phone: '',
  email: '',
  is_main: false,
  errors: {},
})

const companySuggestions = ref([])
const selectedCompany = ref(null)
const innError = ref('')
const isLoadingSuggestions = ref(false)

const isInnValid = computed(() => {
  return form.inn && !innError.value && (form.inn.length === 10 || form.inn.length === 12)
})

const canAddMoreCompanies = computed(() => {
  return companies.value.length < 3
})

onMounted(async () => {
  await loadCompaniesData()
})

const loadCompaniesData = async () => {
  uiState.isLoading = true
  try {
    const response = await $fetch(`${backendUrl}/api/profile`, {
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    if (!response) {
      throw new Error('Пустой ответ от сервера')
    }

    if (Array.isArray(response)) {
      companies.value = response
      mainCompany.value = response.find(c => c.is_main) || null
    } else if (response.id) {
      companies.value = [response]
      mainCompany.value = response.is_main ? response : null
    } else {
      companies.value = response.companies || []
      mainCompany.value = response.mainCompany || null
    }

    companies.value.forEach(company => {
      company.is_main = company.id === (mainCompany.value?.id || null)
    })
  } catch (error) {
    console.log('Ошибка загрузки компаний:', error)
    uiState.error = 'Не удалось загрузить данные компаний. Пожалуйста, попробуйте позже.'
  } finally {
    uiState.isLoading = false
  }
}

const startAdding = () => {
  resetForm()
  uiState.isEditing = true
  uiState.editingCompanyId = null
  uiState.isFirstAdd = true
  uiState.showFullForm = false
}

const startEditing = company => {
  Object.assign(form, {
    name: company.name,
    inn: company.inn,
    kpp: company.kpp,
    address: company.legal_address,
    director: company.director,
    phone: company.phone,
    email: company.email,
    is_main: company.is_main,
  })
  uiState.isEditing = true
  uiState.editingCompanyId = company.id
  uiState.showFullForm = true
}

const resetForm = () => {
  form.name = ''
  form.inn = ''
  form.kpp = ''
  form.address = ''
  form.director = ''
  form.phone = ''
  form.email = ''
  form.is_main = false
  form.errors = {}
  companySuggestions.value = []
  selectedCompany.value = null
  innError.value = ''
}

const cancelEditing = () => {
  uiState.isEditing = false
  uiState.editingCompanyId = null
  uiState.isFirstAdd = false
  uiState.showFullForm = false
  resetForm()
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

  if (companies.value.some(c => c.inn === form.inn && c.id !== uiState.editingCompanyId)) {
    innError.value = 'Компания с таким ИНН уже добавлена'
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
    const response = await $fetch('https://www.tinkoff.ru/api/common/dadata/suggestions/api/4_1/rs/suggest/party', {
      params: {
        appName: 'company-pages',
        query: form.inn,
        count: 5,
      },
    })

    if (response.suggestions?.length) {
      companySuggestions.value = response.suggestions
      uiState.showFullForm = true
    } else {
      innError.value = 'Компания с таким ИНН не найдена'
    }
  } catch (err) {
    console.log('Ошибка при проверке ИНН:', err)
    innError.value = 'Ошибка при проверке ИНН. Попробуйте ввести данные вручную.'
  } finally {
    isLoadingSuggestions.value = false
  }
}

const selectCompanySuggestion = suggestion => {
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

  if (!form.inn.trim()) {
    form.errors.inn = 'Введите ИНН компании'
    isValid = false
  } else if (!isInnValid.value) {
    form.errors.inn = innError.value || 'Некорректный ИНН'
    isValid = false
  }

  if (uiState.showFullForm) {
    if (!form.name.trim()) {
      form.errors.name = 'Введите название компании'
      isValid = false
    }
  }

  return isValid
}

const deleteCompany = async companyId => {
  if (!confirm('Вы уверены, что хотите удалить эту компанию? Это действие нельзя отменить.')) {
    return
  }

  uiState.isLoading = true
  uiState.error = null

  try {
    await $fetch(`${backendUrl}/api/profile/companies/${companyId}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    await loadCompaniesData()
  } catch (error) {
    console.log('Ошибка при удалении компании:', error)
    uiState.error = 'Ошибка при удалении: ' + (error.data?.message || error.message)
  } finally {
    uiState.isLoading = false
  }
}

const setMainCompany = async companyId => {
  uiState.isLoading = true
  uiState.error = null

  try {
    await $fetch(`${backendUrl}/api/profile/companies/${companyId}/set-main`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    await loadCompaniesData()
  } catch (error) {
    console.log('Ошибка при установке основной компании:', error)
    uiState.error = 'Ошибка: ' + (error.data?.message || error.message)
  } finally {
    uiState.isLoading = false
  }
}

const saveCompany = async () => {
  if (!validate()) return

  uiState.isLoading = true
  uiState.error = null

  try {
    const url = uiState.editingCompanyId
      ? `${backendUrl}/api/profile/companies/${uiState.editingCompanyId}`
      : `${backendUrl}/api/profile/companies`

    const method = uiState.editingCompanyId ? 'PUT' : 'POST'

    await $fetch(url, {
      method,
      body: JSON.stringify({
        name: form.name,
        inn: form.inn,
        kpp: form.kpp,
        address: form.address,
        director: form.director,
        phone: form.phone,
        email: form.email,
        legal_address: form.address,
        is_main: form.is_main || companies.value.length === 0,
      }),
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })

    await loadCompaniesData()
    cancelEditing()
  } catch (error) {
    console.log('Ошибка ответа сервера:', error.data)
    uiState.error = 'Ошибка при сохранении: ' + (error.data?.message || error.message)
  } finally {
    uiState.isLoading = false
  }
}

watch(
  () => form.inn,
  newVal => {
    if (newVal.length >= 10 && uiState.isFirstAdd) {
      const delaySearch = setTimeout(() => {
        validateInn()
        clearTimeout(delaySearch)
      }, 800)
    }
  }
)
</script>

<template>
  <div class="min-h-screen bg-gray-50 py-6 md:py-10">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6 md:gap-8 lg:gap-10">
        <SidebarMenu class="hidden md:block" />

        <div class="flex-1 space-y-6 md:space-y-8 lg:space-y-10">
          <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
              <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Мои организации</h1>
              <p v-if="!uiState.isEditing && companies.length > 0" class="text-sm text-gray-500 mt-2">
                Всего организаций: <span class="font-medium">{{ companies.length }}</span>
              </p>
            </div>
            <button
              v-if="canAddMoreCompanies && !uiState.isEditing"
              @click="startAdding"
              class="px-5 py-3 bg-primary from-primary to-primary-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2 text-base font-medium"
            >
              <Icon name="mdi:plus" class="w-5 h-5" />
              <span>Добавить организацию</span>
            </button>
          </div>

          <div v-if="!uiState.isEditing && companies.length > 0" class="grid gap-6 md:gap-8">
            <div
              v-for="company in companies"
              :key="company.id"
              class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all hover:shadow-2xl"
              :class="{ 'ring-4 ring-primary ring-opacity-30': company.is_main }"
            >
              <div class="p-6 md:p-8">
                <div class="flex justify-between items-start mb-6 gap-4">
                  <div class="flex items-start gap-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-xl">
                      <Icon name="mdi:office-building" class="w-7 h-7 text-primary" />
                    </div>
                    <div>
                      <h3 class="text-xl md:text-2xl font-semibold text-gray-900">
                        {{ company.name || `Компания #${company.id}` }}
                      </h3>
                      <div class="flex items-center gap-3 mt-2">
                        <span
                          v-if="company.is_main"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary bg-opacity-15 text-primary border border-primary border-opacity-20"
                        >
                          <Icon name="mdi:star" class="w-4 h-4 mr-1.5" />
                          Основная
                        </span>
                        <span class="text-sm text-gray-500">
                          Добавлена: {{ new Date(company.created_at).toLocaleDateString() }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <button
                    v-if="!company.is_main"
                    @click="setMainCompany(company.id)"
                    class="text-sm text-gray-600 hover:text-primary flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-all"
                    title="Сделать основной"
                  >
                    <Icon name="mdi:star-outline" class="w-5 h-5" />
                    <span class="hidden sm:inline">Основная</span>
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                  <div class="bg-gray-50 rounded-xl p-5 shadow-inner">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                      <Icon name="mdi:card-account-details" class="w-5 h-5 text-primary" />
                      Реквизиты
                    </h4>
                    <dl class="space-y-4">
                      <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">ИНН</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2 font-mono tracking-wide">
                          {{ company.inn }}
                        </dd>
                      </div>
                      <div v-if="company.kpp" class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">КПП</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2 font-mono tracking-wide">
                          {{ company.kpp }}
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="bg-gray-50 rounded-xl p-5 shadow-inner">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                      <Icon name="mdi:account-box-multiple" class="w-5 h-5 text-primary" />
                      Контактные данные
                    </h4>
                    <dl class="space-y-4">
                      <div v-if="company.director" class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Директор</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2">{{ company.director }}</dd>
                      </div>
                      <div v-if="company.legal_address" class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Адрес</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2">{{ company.legal_address }}</dd>
                      </div>
                      <div v-if="company.phone" class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Телефон</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2">
                          <a :href="`tel:${company.phone}`" class="hover:text-primary transition-colors">
                            {{ company.phone }}
                          </a>
                        </dd>
                      </div>
                      <div v-if="company.email" class="grid grid-cols-3 gap-4">
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-sm font-medium text-gray-900 col-span-2">
                          <a :href="`mailto:${company.email}`" class="hover:text-primary transition-colors">
                            {{ company.email }}
                          </a>
                        </dd>
                      </div>
                    </dl>
                  </div>
                </div>

                <div class="mt-8 flex flex-wrap justify-end gap-3">
                  <button
                    @click="startEditing(company)"
                    class="px-5 py-2.5 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all flex items-center gap-2 text-sm font-medium shadow-md hover:shadow-lg"
                  >
                    <Icon name="mdi:pencil" class="w-5 h-5" />
                    <span>Редактировать</span>
                  </button>
                  <button
                    @click="deleteCompany(company.id)"
                    :disabled="uiState.isLoading"
                    class="px-5 py-2.5 bg-white text-red-600 rounded-lg hover:bg-red-50 transition-all flex items-center gap-2 text-sm font-medium shadow-md hover:shadow-lg disabled:opacity-70"
                  >
                    <Icon name="mdi:trash-can-outline" class="w-5 h-5" />
                    <span>Удалить</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="!uiState.isEditing && companies.length === 0"
            class="bg-white rounded-2xl shadow-xl overflow-hidden"
          >
            <div class="p-10 md:p-12 lg:p-16 text-center">
              <div
                class="mx-auto h-24 w-24 rounded-2xl bg-primary bg-opacity-10 flex items-center justify-center mb-8 shadow-inner"
              >
                <Icon name="mdi:office-building" class="w-12 h-12 text-primary" />
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">У вас пока нет добавленных организаций</h3>
              <p class="text-gray-500 mb-8 max-w-md mx-auto text-lg">
                Добавьте информацию о вашей организации для работы с документами и настройками
              </p>
              <button
                @click="startAdding"
                class="px-8 py-4 bg-primary from-primary to-primary-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-3 mx-auto text-base font-medium"
              >
                <Icon name="mdi:plus" class="w-6 h-6" />
                <span>Добавить организацию</span>
              </button>
            </div>
          </div>

          <div v-if="uiState.isEditing" class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-8">
              <div class="flex items-center gap-4 mb-8">
                <button @click="cancelEditing" class="p-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                  <Icon name="mdi:arrow-left" class="w-6 h-6 text-gray-500" />
                </button>
                <h2 class="text-2xl font-bold text-gray-900">
                  {{ uiState.editingCompanyId ? 'Редактирование организации' : 'Добавление организации' }}
                </h2>
              </div>

              <form @submit.prevent="saveCompany" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div>
                    <div class="bg-gray-50 rounded-xl p-6 shadow-inner">
                      <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center gap-3">
                        <Icon name="mdi:card-account-details" class="w-6 h-6 text-primary" />
                        Основная информация
                      </h3>
                      <div class="space-y-6">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-3">ИНН *</label>
                          <input
                            v-model="form.inn"
                            @blur="validateInn"
                            :class="{ 'ring-2 ring-red-400': form.errors.inn || innError }"
                            class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                            placeholder="Введите ИНН компании"
                          />
                          <p v-if="form.errors.inn" class="mt-3 text-sm text-red-600">{{ form.errors.inn }}</p>
                          <p v-if="innError && !form.errors.inn" class="mt-3 text-sm text-red-600">{{ innError }}</p>

                          <div v-if="isLoadingSuggestions" class="mt-4 flex items-center text-gray-500 text-sm">
                            <Icon name="mdi:loading" class="animate-spin mr-3 w-5 h-5" />
                            Поиск компании...
                          </div>

                          <div
                            v-if="companySuggestions.length > 0"
                            class="mt-4 rounded-xl shadow-md bg-white text-base overflow-hidden"
                          >
                            <div
                              v-for="suggestion in companySuggestions"
                              :key="suggestion.data.inn"
                              @click="selectCompanySuggestion(suggestion)"
                              class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                            >
                              <div class="font-medium">{{ suggestion.value }}</div>
                              <div class="text-gray-500 mt-2 text-sm">
                                <span class="font-semibold">ИНН:</span> {{ suggestion.data.inn }}
                                <span v-if="suggestion.data.kpp" class="ml-3">
                                  <span class="font-semibold">КПП:</span> {{ suggestion.data.kpp }}
                                </span>
                              </div>
                              <div class="text-gray-500 mt-2 text-sm">
                                <span class="font-semibold">Адрес:</span>
                                {{ suggestion.data.address?.unrestricted_value || 'не указан' }}
                              </div>
                            </div>
                          </div>

                          <button
                            v-if="selectedCompany"
                            @click="resetCompanySelection"
                            type="button"
                            class="mt-4 text-sm text-primary hover:text-primary-600 flex items-center gap-2 font-medium"
                          >
                            <Icon name="mdi:refresh" class="w-5 h-5" />
                            Сбросить выбор компании
                          </button>
                        </div>

                        <template v-if="uiState.showFullForm">
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Название *</label>
                            <input
                              v-model="form.name"
                              :class="{ 'ring-2 ring-red-400': form.errors.name }"
                              class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                            />
                            <p v-if="form.errors.name" class="mt-3 text-sm text-red-600">{{ form.errors.name }}</p>
                          </div>

                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">КПП</label>
                            <input
                              v-model="form.kpp"
                              class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                            />
                          </div>

                          <div v-if="companies.length > 0" class="mt-4">
                            <CheckBox v-model="form.is_main" title="Сделать основной компанией" />
                          </div>
                        </template>
                      </div>
                    </div>
                  </div>

                  <div v-if="uiState.showFullForm">
                    <div class="bg-gray-50 rounded-xl p-6 shadow-inner">
                      <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center gap-3">
                        <Icon name="mdi:account-box-multiple" class="w-6 h-6 text-primary" />
                        Контактная информация
                      </h3>
                      <div class="space-y-6">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-3">Юридический адрес</label>
                          <input
                            v-model="form.address"
                            class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-3">Директор</label>
                          <input
                            v-model="form.director"
                            class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-3">Телефон</label>
                          <input
                            v-model="form.phone"
                            class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-3">Email</label>
                          <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-5 py-3 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:ring-opacity-50 text-base transition-all"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pt-6 flex justify-end gap-4">
                  <button
                    type="button"
                    @click="cancelEditing"
                    class="px-6 py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all shadow-md hover:shadow-lg text-base font-medium"
                  >
                    Отменить
                  </button>
                  <button
                    type="submit"
                    :disabled="uiState.isLoading"
                    class="px-6 py-3 bg-primary from-primary to-primary-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-70 text-base font-medium flex items-center gap-2"
                  >
                    <Icon v-if="uiState.isLoading" name="mdi:loading" class="animate-spin w-5 h-5" />
                    <span>{{ uiState.editingCompanyId ? 'Сохранить изменения' : 'Добавить организацию' }}</span>
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
