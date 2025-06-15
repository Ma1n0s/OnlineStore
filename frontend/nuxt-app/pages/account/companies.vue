<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useUserStore } from '~/stores/user'
import SidebarMenu from '~/components/Account/SidebarMenu.vue'

definePageMeta({
  middleware: ['auth'],
})
import CheckBox from '~/components/Account/CheckBox.vue'

const {
  public: { backendUrl },
} = useRuntimeConfig()

const {
  public: { dadataApiToken },
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
    await userStore.fetchUser()

    if (userStore.user?.companies) {
      companies.value = userStore.user.companies
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
    address: company.address,
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

const searchCompanyByINN = async () => {
  if (!isInnValid.value) return;

  isLoadingSuggestions.value = true;
  companySuggestions.value = [];

  try {
    const response = await $fetch('https://www.tinkoff.ru/api/common/dadata/suggestions/api/4_1/rs/suggest/party', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Token ${dadataApiToken}`
      },
      body: JSON.stringify({
        query: form.inn,
        count: 5
      })
    });

    if (response?.suggestions?.length) {
      companySuggestions.value = response.suggestions;
      uiState.showFullForm = true;
    } else {
      innError.value = 'Компания с таким ИНН не найдена';
    }
  } catch (err) {
    console.error('Ошибка при проверке ИНН:', err);
    innError.value = 'Ошибка при проверке ИНН';
  } finally {
    isLoadingSuggestions.value = false;
  }
};

const selectCompanySuggestion = suggestion => {
  selectedCompany.value = suggestion;
  form.name = suggestion.value || '';
  form.kpp = suggestion.data.kpp || '';
  form.address = suggestion.data.address?.unrestricted_value || suggestion.data.address?.value || '';
  form.director = suggestion.data.management?.name || '';
  companySuggestions.value = [];
};

const validate = () => {
  let isValid = true;
  form.errors = {};

  if (!form.inn.trim()) {
    form.errors.inn = 'Введите ИНН компании';
    isValid = false;
  } else if (!isInnValid.value) {
    form.errors.inn = innError.value || 'Некорректный ИНН';
    isValid = false;
  }

  if (uiState.showFullForm) {
    if (!form.name.trim()) {
      form.errors.name = 'Введите название компании';
      isValid = false;
    }
    if (!form.address.trim()) {
      form.errors.address = 'Введите адрес компании';
      isValid = false;
    }
    if (!form.director.trim()) {
      form.errors.director = 'Введите ФИО директора';
      isValid = false;
    }
  }

  return isValid;
};

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

  // Проверка на уникальность ИНН среди уже добавленных компаний
  if (companies.value.some(c => c.inn === form.inn && c.id !== uiState.editingCompanyId)) {
    innError.value = 'Компания с таким ИНН уже добавлена'
    return false
  }

  innError.value = ''
  await searchCompanyByINN()
  return true
}

const resetCompanySelection = () => {
  selectedCompany.value = null
  companySuggestions.value = []
  form.name = ''
  form.kpp = ''
  form.address = ''
  form.director = ''
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

    // Show success message
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
  <div class="min-h-screen py-8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-4 lg:px-8">
      <div class="flex flex-col md:flex-row gap-6">
        <SidebarMenu class="hidden lg:block" />

        <div class="flex-1 space-y-6">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 shadow-2xl">Мои организации</h1>
            <button
              v-if="canAddMoreCompanies && !uiState.isEditing"
              @click="startAdding"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2"
            >
              <Icon name="mdi:plus" class="w-5 h-5" />
              Добавить компанию
            </button>
          </div>

          <div v-if="!uiState.isEditing && companies.length > 0" class="space-y-6">
            <div
              v-for="company in companies"
              :key="company.id"
              class="bg-white shadow-2xl rounded-lg overflow-hidden"
              :class="{ 'border-2 border-primary': company.is_main }"
            >
              <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ company.name || `Компания #${company.id}` }}
                    <span v-if="company.is_main" class="ml-2 text-xs bg-primary text-white px-2 py-1 rounded"
                      >Основная</span
                    >
                  </h3>
                  <div class="flex gap-2">
                    <button
                      v-if="!company.is_main"
                      @click="setMainCompany(company.id)"
                      class="text-sm text-primary hover:text-primary-hover flex items-center gap-1"
                    >
                      <Icon name="mdi:star-outline" class="w-4 h-4" />
                      Сделать основной
                    </button>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Основная информация</h4>
                    <div class="space-y-4">
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
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Контактная информация</h4>
                    <div class="space-y-4">
                      <div v-if="company.address">
                        <p class="text-sm text-gray-500">Юридический адрес</p>
                        <p class="text-gray-900 font-medium">{{ company.address }}</p>
                      </div>
                      <div v-if="company.director">
                        <p class="text-sm text-gray-500">Директор</p>
                        <p class="text-gray-900 font-medium">{{ company.director }}</p>
                      </div>
                      <div v-if="company.phone">
                        <p class="text-sm text-gray-500">Телефон</p>
                        <p class="text-gray-900 font-medium">{{ company.phone }}</p>
                      </div>
                      <div v-if="company.email">
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-900 font-medium">{{ company.email }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-6 pt-4 border-t flex justify-end gap-3">
                  <button
                    @click="startEditing(company)"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2"
                  >
                    <Icon name="mdi:pencil" class="w-5 h-5" />
                    Редактировать
                  </button>
                  <button
                    @click="deleteCompany(company.id)"
                    :disabled="uiState.isLoading"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 disabled:opacity-70"
                  >
                    <Icon name="mdi:trash-can-outline" class="w-5 h-5" />
                    Удалить
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="!uiState.isEditing && companies.length === 0"
            class="bg-white shadow-2xl rounded-lg overflow-hidden"
          >
            <div class="p-6 text-center py-12">
              <Icon name="mdi:office-building" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">Данные компаний не добавлены</h3>
              <p class="text-gray-500 mb-6">Добавьте информацию о вашей компании для работы с документами</p>
              <button
                @click="startAdding"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition flex items-center gap-2 mx-auto"
              >
                <Icon name="mdi:plus" class="w-5 h-5" />
                Добавить компанию
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
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                          placeholder="Введите ИНН компании"
                        />
                        <p v-if="form.errors.inn" class="mt-1 text-sm text-red-600">{{ form.errors.inn }}</p>
                        <p v-if="innError && !form.errors.inn" class="mt-1 text-sm text-red-600">{{ innError }}</p>

                        <div v-if="isLoadingSuggestions" class="mt-2 flex items-center text-gray-500">
                          <Icon name="mdi:loading" class="animate-spin mr-2" />
                          Поиск компании...
                        </div>

                        <div
                          v-if="companySuggestions.length > 0"
                          class="mt-2 border rounded-lg shadow-sm bg-white"
                          style="max-height: 300px; overflow-y: auto"
                        >
                          <div
                            v-for="suggestion in companySuggestions"
                            :key="suggestion.data.inn"
                            @click="selectCompanySuggestion(suggestion)"
                            class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors"
                          >
                            <div class="font-medium">{{ suggestion.value }}</div>
                            <div class="text-sm text-gray-500 mt-1">
                              <span class="font-semibold">ИНН:</span> {{ suggestion.data.inn }}
                              <span v-if="suggestion.data.kpp" class="ml-2">
                                <span class="font-semibold">КПП:</span> {{ suggestion.data.kpp }}
                              </span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                              <span class="font-semibold">Адрес:</span>
                              {{ suggestion.data.address?.unrestricted_value || 'не указан' }}
                            </div>
                            <div v-if="suggestion.data.management?.name" class="text-sm text-gray-500 mt-1">
                              <span class="font-semibold">Руководитель:</span> {{ suggestion.data.management.name }}
                            </div>
                          </div>
                        </div>

                        <button
                          v-if="selectedCompany"
                          @click="resetCompanySelection"
                          type="button"
                          class="mt-2 text-sm text-black hover:text-gray-600"
                        >
                          Сбросить выбор компании
                        </button>
                      </div>

                      <!-- Остальные поля показываются только после проверки ИНН -->
                      <template v-if="uiState.showFullForm">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                          <input
                            v-model="form.name"
                            :class="{ 'border-red-300': form.errors.name }"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                          />
                          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">КПП</label>
                          <input
                            v-model="form.kpp"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                          />
                        </div>

                        <div v-if="companies.length > 0">
                          <CheckBox v-model="form.is_main" title="Сделать основной компанией" />
                        </div>
                      </template>
                    </div>
                  </div>

                  <div v-if="uiState.showFullForm">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Контактная информация</h3>
                    <div class="space-y-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Юридический адрес</label>
                        <input
                          v-model="form.address"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Директор</label>
                        <input
                          v-model="form.director"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                        <input
                          v-model="form.phone"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
                        />
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input
                          v-model="form.email"
                          type="email"
                          class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-primary focus:border-primary"
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
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition disabled:opacity-70"
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
