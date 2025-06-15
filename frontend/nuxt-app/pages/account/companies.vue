<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'
import CheckBox from '~/components/Account/CheckBox.vue'

definePageMeta({
  middleware: ['auth'],
})

const {
  public: { backendUrl },
} = useRuntimeConfig()

const userStore = useUserStore()
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
    
    if (response.user?.companies) {
      companies.value = response.user.companies
      mainCompany.value = response.mainCompany
      
      // Обновляем флаг is_main для всех компаний
      companies.value.forEach(company => {
        company.is_main = company.id === mainCompany.value?.id
      })
    }
  } catch (error) {
    uiState.error = 'Ошибка при загрузке данных компаний: ' + error.message
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
    console.error('Ошибка при проверке ИНН:', err)
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
    console.error('Ошибка при удалении компании:', error)
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
    console.error('Ошибка при установке основной компании:', error)
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
    console.error('Ошибка ответа сервера:', error.data)
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
  <div class="min-h-screen py-4 md:py-8">
    <div class="max-w-screen-2xl mx-auto px-2 sm:px-4">
      <div class="flex flex-col md:flex-row gap-4 md:gap-6">
        <SidebarMenu class="hidden md:block" />

        <div class="flex-1 space-y-4 md:space-y-6">
          <div class="flex items-center justify-between flex-wrap gap-2">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Мои организации</h1>
            <button
              v-if="canAddMoreCompanies && !uiState.isEditing"
              @click="startAdding"
              class="px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-1 md:gap-2 text-sm md:text-base"
            >
              <Icon name="mdi:plus" class="w-4 h-4 md:w-5 md:h-5" />
              <span>Добавить</span>
            </button>
          </div>

          <div v-if="!uiState.isEditing && companies.length > 0" class="space-y-4 md:space-y-6">
            <div
              v-for="company in companies"
              :key="company.id"
              class="bg-white shadow rounded-lg overflow-hidden"
              :class="{ 'border-2 border-primary': company.is_main }"
            >
              <div class="p-4 md:p-6">
                <div class="flex justify-between items-start mb-3 md:mb-4 flex-wrap gap-2">
                  <h3 class="text-base md:text-lg font-medium text-gray-900">
                    {{ company.name || `Компания #${company.id}` }}
                    <span
                      v-if="company.is_main"
                      class="ml-1 md:ml-2 text-xs bg-primary text-white px-1.5 py-0.5 md:px-2 md:py-1 rounded"
                      >Основная</span
                    >
                  </h3>
                  <div class="flex gap-1 md:gap-2">
                    <button
                      v-if="!company.is_main"
                      @click="setMainCompany(company.id)"
                      class="text-xs md:text-sm text-primary hover:text-primary-hover flex items-center gap-1"
                    >
                      <Icon name="mdi:star-outline" class="w-3 h-3 md:w-4 md:h-4" />
                      <span class="hidden sm:inline">Сделать основной</span>
                      <span class="sm:hidden">Основная</span>
                    </button>
                  </div>
                </div>

                <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 gap-4 md:gap-6">
                  <div>
                    <h4 class="text-xs md:text-sm font-medium text-gray-500 mb-2 md:mb-3">Основная информация</h4>
                    <div class="space-y-2 md:space-y-4">
                      <div>
                        <p class="text-xs md:text-sm text-gray-500">ИНН</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.inn }}</p>
                      </div>
                      <div v-if="company.kpp">
                        <p class="text-xs md:text-sm text-gray-500">КПП</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.kpp }}</p>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h4 class="text-xs md:text-sm font-medium text-gray-500 mb-2 md:mb-3">Контактная информация</h4>
                    <div class="space-y-2 md:space-y-4">
                      <div v-if="company.legal_address">
                        <p class="text-xs md:text-sm text-gray-500">Юридический адрес</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.legal_address }}</p>
                      </div>
                      <div v-if="company.director">
                        <p class="text-xs md:text-sm text-gray-500">Директор</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.director }}</p>
                      </div>
                      <div v-if="company.phone">
                        <p class="text-xs md:text-sm text-gray-500">Телефон</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.phone }}</p>
                      </div>
                      <div v-if="company.email">
                        <p class="text-xs md:text-sm text-gray-500">Email</p>
                        <p class="text-gray-900 font-medium text-sm md:text-base">{{ company.email }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-4 md:mt-6 pt-3 md:pt-4 border-t flex justify-end gap-2 md:gap-3 flex-wrap">
                  <button
                    @click="startEditing(company)"
                    class="px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-1 md:gap-2 text-sm md:text-base"
                  >
                    <Icon name="mdi:pencil" class="w-4 h-4 md:w-5 md:h-5" />
                    <span>Изменить</span>
                  </button>
                  <button
                    @click="deleteCompany(company.id)"
                    :disabled="uiState.isLoading"
                    class="px-3 py-1.5 md:px-4 md:py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-1 md:gap-2 text-sm md:text-base disabled:opacity-70"
                  >
                    <Icon name="mdi:trash-can-outline" class="w-4 h-4 md:w-5 md:h-5" />
                    <span>Удалить</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="!uiState.isEditing && companies.length === 0" class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 md:p-6 text-center py-8 md:py-12">
              <Icon name="mdi:office-building" class="w-10 h-10 md:w-12 md:h-12 text-gray-400 mx-auto mb-3 md:mb-4" />
              <h3 class="text-base md:text-lg font-medium text-gray-900 mb-1 md:mb-2">Данные компаний не добавлены</h3>
              <p class="text-gray-500 text-sm md:text-base mb-4 md:mb-6">
                Добавьте информацию о вашей компании для работы с документами
              </p>
              <button
                @click="startAdding"
                class="px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-1 md:gap-2 mx-auto text-sm md:text-base"
              >
                <Icon name="mdi:plus" class="w-4 h-4 md:w-5 md:h-5" />
                <span>Добавить компанию</span>
              </button>
            </div>
          </div>

          <div v-if="uiState.isEditing" class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4 md:p-6">
              <form @submit.prevent="saveCompany" class="space-y-4 md:space-y-6">
                <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 gap-4 md:gap-6">
                  <div>
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4">Основная информация</h3>
                    <div class="space-y-3 md:space-y-4">
                      <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">ИНН *</label>
                        <input
                          v-model="form.inn"
                          @blur="validateInn"
                          :class="{ 'border-red-300': form.errors.inn || innError }"
                          class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                          placeholder="Введите ИНН компании"
                        />
                        <p v-if="form.errors.inn" class="mt-1 text-xs md:text-sm text-red-600">{{ form.errors.inn }}</p>
                        <p v-if="innError && !form.errors.inn" class="mt-1 text-xs md:text-sm text-red-600">
                          {{ innError }}
                        </p>

                        <div
                          v-if="isLoadingSuggestions"
                          class="mt-2 flex items-center text-gray-500 text-xs md:text-sm"
                        >
                          <Icon name="mdi:loading" class="animate-spin mr-1 md:mr-2 w-3 h-3 md:w-4 md:h-4" />
                          Поиск компании...
                        </div>

                        <div
                          v-if="companySuggestions.length > 0"
                          class="mt-2 border rounded-lg shadow-sm bg-white text-xs md:text-sm"
                          style="max-height: 200px; overflow-y: auto"
                        >
                          <div
                            v-for="suggestion in companySuggestions"
                            :key="suggestion.data.inn"
                            @click="selectCompanySuggestion(suggestion)"
                            class="p-2 md:p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors"
                          >
                            <div class="font-medium">{{ suggestion.value }}</div>
                            <div class="text-gray-500 mt-1">
                              <span class="font-semibold">ИНН:</span> {{ suggestion.data.inn }}
                              <span v-if="suggestion.data.kpp" class="ml-1 md:ml-2">
                                <span class="font-semibold">КПП:</span> {{ suggestion.data.kpp }}
                              </span>
                            </div>
                            <div class="text-gray-500 mt-1">
                              <span class="font-semibold">Адрес:</span>
                              {{ suggestion.data.address?.unrestricted_value || 'не указан' }}
                            </div>
                            <div v-if="suggestion.data.management?.name" class="text-gray-500 mt-1">
                              <span class="font-semibold">Руководитель:</span> {{ suggestion.data.management.name }}
                            </div>
                          </div>
                        </div>

                        <button
                          v-if="selectedCompany"
                          @click="resetCompanySelection"
                          type="button"
                          class="mt-2 text-xs md:text-sm text-black hover:text-gray-600"
                        >
                          Сбросить выбор компании
                        </button>
                      </div>

                      <template v-if="uiState.showFullForm">
                        <div>
                          <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Название *</label>
                          <input
                            v-model="form.name"
                            :class="{ 'border-red-300': form.errors.name }"
                            class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                          />
                          <p v-if="form.errors.name" class="mt-1 text-xs md:text-sm text-red-600">
                            {{ form.errors.name }}
                          </p>
                        </div>

                        <div>
                          <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">КПП</label>
                          <input
                            v-model="form.kpp"
                            class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                          />
                        </div>

                        <div v-if="companies.length > 0" class="mt-2">
                          <CheckBox v-model="form.is_main" title="Сделать основной компанией" />
                        </div>
                      </template>
                    </div>
                  </div>

                  <div v-if="uiState.showFullForm">
                    <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4">Контактная информация</h3>
                    <div class="space-y-3 md:space-y-4">
                      <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Юридический адрес</label>
                        <input
                          v-model="form.address"
                          class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                        />
                      </div>
                      <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Директор</label>
                        <input
                          v-model="form.director"
                          class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                        />
                      </div>
                      <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Телефон</label>
                        <input
                          v-model="form.phone"
                          class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                        />
                      </div>
                      <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                          v-model="form.email"
                          type="email"
                          class="w-full px-3 py-1.5 md:px-4 md:py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm md:text-base"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="pt-3 md:pt-4 flex justify-end gap-2 md:gap-3 flex-wrap">
                  <button
                    type="button"
                    @click="cancelEditing"
                    class="px-3 py-1.5 md:px-4 md:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-sm md:text-base"
                  >
                    Отмена
                  </button>
                  <button
                    type="submit"
                    :disabled="uiState.isLoading"
                    class="px-3 py-1.5 md:px-4 md:py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition disabled:opacity-70 text-sm md:text-base"
                  >
                    <span v-if="uiState.isLoading" class="flex items-center">
                      <Icon name="mdi:loading" class="animate-spin mr-1 md:mr-2 w-4 h-4" />
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