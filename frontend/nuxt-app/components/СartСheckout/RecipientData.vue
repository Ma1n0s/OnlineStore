<script setup>
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '../ui/Button/Button.vue'
import Modal from '../Modal/Modal.vue'
import axios from 'axios'

const props = defineProps({
  customer: {
    type: Object,
    required: true,
  },
  showSecondForm: {
    type: Boolean,
    required: true,
  },
  secondCustomer: {
    type: Object,
    required: true,
  },
  deliveryAddress: {
    type: String,
    required: true,
  },
  paymentMethod: {
    type: String,
    required: true,
  },
})

const emit = defineEmits([
  'update:showSecondForm',
  'update:secondCustomer',
  'update:deliveryAddress',
  'update:paymentMethod',
])

// Реактивные данные для модалки юрлица
const isLegalEntityModalOpen = ref(false)
const innNumber = ref('')
const innError = ref('')
const companySuggestions = ref([])
const selectedCompany = ref(null)
const isSameAddress = ref(true)
const isLoading = ref(false)

// Данные компании
const companyData = reactive({
  name: '',
  inn: '',
  kpp: '',
  ogrn: '',
  legalAddress: '',
  actualAddress: '',
})

// Вычисляемое свойство для валидности ИНН
const isInnValid = computed(() => {
  return innNumber.value && !innError.value && (innNumber.value.length === 10 || innNumber.value.length === 12)
})

// Метод поиска компании по ИНН
const searchCompanyByINN = async () => {
  if (!isInnValid.value) return

  isLoading.value = true
  companySuggestions.value = []

  try {
    const { data } = await axios.get('https://www.tinkoff.ru/api/common/dadata/suggestions/api/4_1/rs/suggest/party', {
      params: {
        appName: 'company-pages',
        query: innNumber.value,
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
    isLoading.value = false
  }
}

// Метод выбора компании из списка
const selectCompany = company => {
  selectedCompany.value = company
  companyData.name = company.value
  companyData.inn = company.data.inn
  companyData.kpp = company.data.kpp || ''
  companyData.ogrn = company.data.ogrn || ''
  companyData.legalAddress = company.data.address?.unrestricted_value || ''
  companyData.actualAddress = company.data.address?.unrestricted_value || ''
}

// Метод валидации ИНН
const validateInn = async () => {
  if (!innNumber.value) {
    innError.value = 'Поле обязательно для заполнения'
    return false
  }

  const innRegex = /^\d+$/
  if (!innRegex.test(innNumber.value)) {
    innError.value = 'ИНН должен содержать только цифры'
    return false
  }

  if (innNumber.value.length !== 10 && innNumber.value.length !== 12) {
    innError.value = 'ИНН должен содержать 10 или 12 цифр'
    return false
  }

  innError.value = ''
  await searchCompanyByINN()
  return true
}

// Метод сброса выбора компании
const resetCompanySelection = () => {
  selectedCompany.value = null
  companySuggestions.value = []
}

// Метод сохранения данных юрлица
const saveLegalEntity = () => {
  // Здесь можно добавить логику сохранения
  console.log('Сохранены данные компании:', companyData)
  isLegalEntityModalOpen.value = false
  resetCompanyData()
}

// Сброс данных компании
const resetCompanyData = () => {
  innNumber.value = ''
  innError.value = ''
  companySuggestions.value = []
  selectedCompany.value = null
  isSameAddress.value = true
  Object.assign(companyData, {
    name: '',
    inn: '',
    kpp: '',
    ogrn: '',
    legalAddress: '',
    actualAddress: '',
  })
}

// Обработчик изменения второго получателя
const updateSecondCustomer = (field, value) => {
  emit('update:secondCustomer', { ...props.secondCustomer, [field]: value })
}
</script>

<template>
  <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm">
    <div>
      <!-- Заголовок с кнопкой юрлица -->
      <div class="flex justify-between items-start mb-4">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Данные получателя</h2>
        <button
          @click="isLegalEntityModalOpen = true"
          class="text-sm font-medium text-primary hover:text-primary-dark underline"
        >
          Покупаю как юрлицо
        </button>
      </div>

      <div class="flex items-center mb-4 sm:mb-6">
        <div class="flex items-center text-xs sm:text-sm text-gray-500">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <span>Все поля обязательны</span>
        </div>
      </div>

      <div class="flex gap-3 sm:gap-4 mb-3 sm:mb-4">
        <TextInput disabled id="name" label="Имя" :modelValue="customer.name" placeholder="Ваше имя" size="small" />
        <TextInput
          id="phone"
          disabled
          label="Телефон"
          type="tel"
          :modelValue="customer.phone"
          placeholder="+7 (___) ___-__-__"
          size="small"
        />
      </div>

      <div class="mb-3 sm:mb-4">
        <label class="inline-flex items-center cursor-pointer">
          <input
            type="checkbox"
            :checked="showSecondForm"
            @change="$emit('update:showSecondForm', $event.target.checked)"
            class="sr-only peer"
          />
          <div
            class="bg-black relative w-9 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-primary-active"
          ></div>
          <span class="ms-2 text-xs sm:text-sm font-medium text-gray-700">Другой получатель</span>
        </label>
      </div>

      <div v-if="showSecondForm" class="grid grid-cols-1 gap-3 sm:gap-4 mb-3 sm:mb-4">
        <TextInput
          id="secondName"
          label="Имя"
          :modelValue="secondCustomer.name"
          @update:modelValue="val => updateSecondCustomer('name', val)"
          placeholder="Имя получателя"
          size="small"
        />
        <TextInput
          id="secondPhone"
          label="Телефон"
          type="tel"
          :modelValue="secondCustomer.phone"
          @update:modelValue="val => updateSecondCustomer('phone', val)"
          placeholder="+7 (___) ___-__-__"
          size="small"
        />
      </div>

      <div class="mb-3 sm:mb-4">
        <TextInput
          id="address"
          label="Адрес доставки"
          :modelValue="deliveryAddress"
          @update:modelValue="$emit('update:deliveryAddress', $event)"
          placeholder="Город, улица, дом, квартира"
          size="small"
        />
      </div>

      <!-- Модальное окно для юрлица -->
      <Modal :isOpen="isLegalEntityModalOpen" title="Регистрация юрлица" @close="isLegalEntityModalOpen = false">
        <div class="space-y-4">
          <TextInput
            id="inn"
            label="ИНН организации или ИП"
            v-model="innNumber"
            placeholder="Введите ИНН"
            @input="resetCompanySelection"
            @blur="validateInn"
            :error="innError"
            :disabled="selectedCompany"
          />

          <!-- Индикатор загрузки -->
          <div v-if="isLoading" class="flex justify-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
          </div>

          <!-- Блок выбора компании -->
          <div
            v-if="companySuggestions.length > 0 && !selectedCompany"
            class="border border-gray-200 rounded-lg overflow-hidden"
          >
            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-700">Найдены организации:</h4>
            </div>

            <div class="divide-y divide-gray-200">
              <div
                v-for="company in companySuggestions"
                :key="company.data.inn"
                class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                @click="selectCompany(company)"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-medium text-gray-900">{{ company.value }}</div>
                    <div class="text-sm text-gray-500 mt-1">
                      <span class="font-medium">ИНН:</span> {{ company.data.inn }}
                      <span v-if="company.data.kpp" class="ml-2">
                        <span class="font-medium">КПП:</span> {{ company.data.kpp }}
                      </span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                      <span class="font-medium">Адрес:</span>
                      {{ company.data.address?.unrestricted_value || 'Не указан' }}
                    </div>
                  </div>
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 text-center border-t border-gray-200">
              <button
                type="button"
                class="text-sm font-medium text-primary hover:text-primary-dark"
                @click="resetCompanySelection"
              >
                Найти другой ИНН
              </button>
            </div>
          </div>

          <!-- Данные выбранной компании -->
          <div v-if="selectedCompany" class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-medium text-gray-900">{{ companyData.name }}</h4>
                  <div class="text-sm text-gray-500 mt-1">
                    <span class="font-medium">ИНН:</span> {{ companyData.inn }}
                    <span v-if="companyData.kpp" class="ml-2">
                      <span class="font-medium">КПП:</span> {{ companyData.kpp }}
                    </span>
                  </div>
                </div>
                <button
                  type="button"
                  class="text-sm font-medium text-primary hover:text-primary-dark"
                  @click="resetCompanySelection"
                >
                  Изменить
                </button>
              </div>
            </div>

            <TextInput label="Юридический адрес" v-model="companyData.legalAddress" disabled multiline />

            <label class="flex items-start space-x-3">
              <input
                type="checkbox"
                v-model="isSameAddress"
                class="mt-1 rounded border-gray-300 text-primary focus:ring-primary"
              />
              <span class="text-sm text-gray-700">Совпадает с фактическим</span>
            </label>

            <TextInput v-if="!isSameAddress" label="Фактический адрес" placeholder="Введите фактический адрес" />
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button
              @click="isLegalEntityModalOpen = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
            >
              Отмена
            </button>
            <button
              @click="saveLegalEntity"
              class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-md transition-colors"
              :disabled="!selectedCompany"
              :class="{ 'opacity-50 cursor-not-allowed': !selectedCompany }"
            >
              Сохранить
            </button>
          </div>
        </div>
      </Modal>
    </div>

    <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-gray-100">
      <h3 class="text-lg sm:text-xl font-bold mb-4 sm:mb-5 text-gray-900">Способ оплаты</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
        <label
          class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
          :class="{ 'border-primary shadow-md': paymentMethod === 'cash' }"
        >
          <input
            type="radio"
            :checked="paymentMethod === 'cash'"
            @change="$emit('update:paymentMethod', 'cash')"
            class="hidden"
          />
          <div class="flex items-start">
            <div
              class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
              :class="{ 'border-primary bg-primary': paymentMethod === 'cash' }"
            >
              <div v-if="paymentMethod === 'cash'" class="h-2 w-2 rounded-full bg-white"></div>
            </div>
            <div>
              <div class="text-base sm:text-lg font-semibold text-gray-900">Наличные</div>
              <p class="text-sm sm:text-base text-gray-500 mt-1">Оплата наличными</p>
            </div>
          </div>
        </label>

        <label
          class="relative bg-white p-4 sm:p-5 rounded-xl cursor-pointer border border-gray-200 hover:border-primary transition-all shadow-sm hover:shadow-md"
          :class="{ 'border-primary shadow-md': paymentMethod === 'card' }"
        >
          <input
            type="radio"
            :checked="paymentMethod === 'card'"
            @change="$emit('update:paymentMethod', 'card')"
            class="hidden"
          />
          <div class="flex items-start">
            <div
              class="flex items-center justify-center h-5 w-5 rounded-full border-2 border-gray-300 mr-3 mt-0.5 flex-shrink-0"
              :class="{ 'border-primary bg-primary': paymentMethod === 'card' }"
            >
              <div v-if="paymentMethod === 'card'" class="h-2 w-2 rounded-full bg-white"></div>
            </div>
            <div>
              <div class="text-base sm:text-lg font-semibold text-gray-900">Картой онлайн</div>
              <p class="text-sm sm:text-base text-gray-500 mt-1">Оплата на сайте</p>
            </div>
          </div>
        </label>
      </div>
    </div>
  </div>
</template>
