<script setup>
import { ref, computed } from 'vue'
import TextInput from '../ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
import { DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'

const props = defineProps({
  product: {
    type: Object,
    required: true,
    default: () => ({
      id: null,
      price: {
        total: 0,
        sum: 0,
        discount: 0,
      },
    }),
  },
})

const isRentalModalOpen = ref(false)
const rentalDays = ref(1)
const rentalPrice = ref(0)
const showSuccessMessage = ref(false)
const showErrorMessage = ref(false)
const messageText = ref('')
const isLoading = ref(false)

const dateRange = ref({
  start: new Date(),
  end: new Date(new Date().setDate(new Date().getDate() + 1)),
})

const formatPrice = value => {
  return Number(value || 0).toLocaleString('ru-RU')
}

const currentPrice = computed(() => formatPrice(props.product?.price?.total))
// const oldPrice = computed(() => formatPrice(props.product?.price?.sum))
// const discount = computed(() => formatPrice(props.product?.price?.discount))
// const hasDiscount = computed(() => props.product?.price?.sum && props.product.price.sum !== props.product.price.total)

const showMessage = (message, isError = false) => {
  messageText.value = message
  if (isError) {
    showErrorMessage.value = true
  } else {
    showSuccessMessage.value = true
  }
  setTimeout(() => {
    showSuccessMessage.value = false
    showErrorMessage.value = false
  }, 3000)
}

const openRentalModal = () => {
  isRentalModalOpen.value = true
  calculateRentalPrice()
}

const closeRentalModal = () => {
  isRentalModalOpen.value = false
}

const calculateRentalPrice = () => {
  if (dateRange.value.start && dateRange.value.end) {
    const diffTime = Math.abs(dateRange.value.end - dateRange.value.start)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    rentalDays.value = Math.max(diffDays, 1)
    rentalPrice.value = Number(props.product?.price?.total || 0) * rentalDays.value
  }
}

const confirmRental = async () => {
  try {
    await addToCart({
      rental_days: rentalDays.value,
      rental_start: dateRange.value.start.toISOString(),
      rental_end: dateRange.value.end.toISOString(),
      rental_price: rentalPrice.value,
    })
    showMessage('Товар добавлен в корзину')
    closeRentalModal()
  } catch (error) {
    showMessage('Ошибка при добавлении товара', true)
    console.error('Rental error:', error)
  }
}

const addToCart = async (options = {}) => {
  if (!props.product?.id) {
    showMessage('Ошибка: товар не доступен', true)
    return
  }

  isLoading.value = true
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/cart', {
      method: 'POST',
      body: JSON.stringify({
        product_id: props.product.id,
        quantity: 1,
        options: options,
      }),
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    })

    showMessage('Товар успешно добавлен в корзину')
    return response
  } catch (error) {
    console.error('Error adding to cart:', error)
    showMessage('Ошибка при добавлении товара', true)
    throw error
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-3 h-full flex flex-col">
    <transition name="fade">
      <div v-if="showSuccessMessage" class="fixed top-4 right-4 z-50">
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-800 px-3 py-2 rounded-lg shadow-lg flex items-center text-sm">
          <Icon name="material-symbols:check-circle" class="w-4 h-4 mr-1" />
          {{ messageText }}
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="showErrorMessage" class="fixed top-4 right-4 z-50">
        <div class="bg-rose-100 border border-rose-400 text-rose-800 px-3 py-2 rounded-lg shadow-lg flex items-center text-sm">
          <Icon name="material-symbols:error" class="w-4 h-4 mr-1" />
          {{ messageText }}
        </div>
      </div>
    </transition>

    <div class="bg-slate-100 rounded-xl shadow-md border-gray-100 p-4">
      <div class="flex items-center justify-between mb-2">
        <p class="text-2xl font-bold">{{ product.price }} ₽</p>
        <p v-if="product.price.discount" class="text-primary font-medium text-sm">
          Экономия {{ product.price.discount }} ₽
        </p>
      </div>
      
      <Button
        @click="addToCart"
        :disabled="isLoading || !product?.id"
        class="w-full bg-primary hover:bg-primary text-white py-2 px-4 rounded-lg font-medium text-sm flex items-center justify-center gap-1"
      >
        <Icon name="material-symbols:shopping-cart-rounded" class="h-4 w-4" />
        <span>{{ isLoading ? 'Добавление...' : 'В корзину' }}</span>
      </Button>
    </div>

    <div class="bg-slate-100 rounded-xl shadow-md border-gray-100 p-3 flex items-center gap-2">
      <div class="bg-primary/10 p-1.5 rounded-lg">
        <Icon name="mdi:percent-box" class="h-4 w-4 text-primary" />
      </div>
      <div>
        <p class="font-medium text-sm">+900 Кешбэк</p>
        <p class="text-xs text-gray-500">Баллы для следующих покупок</p>
      </div>
    </div>

    <div class="bg-slate-100 rounded-xl shadow-md border-gray-100 p-3">
      <div class="flex items-start gap-2 mb-1">
        <div class="bg-primary/10 p-1.5 rounded-lg">
          <Icon name="mdi:store" class="h-4 w-4 text-primary" />
        </div>
        <div>
          <p class="font-medium text-sm">Самовывоз сегодня</p>
          <p class="text-xs text-gray-500">Из 13 магазинов, бесплатно</p>
        </div>
      </div>
    </div>

    <Modal :isOpen="isRentalModalOpen" title="Аренда товара" @close="closeRentalModal">
      <div class="mt-6 space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Выберите даты аренды</label>
          <DatePicker
            v-model.range="dateRange"
            @update:modelValue="calculateRentalPrice"
            :min-date="new Date()"
            is-range
            :columns="1"
            :masks="{ input: 'DD.MM.YYYY' }"
            locale="ru"
          >
            <template #default="{ inputValue, inputEvents }">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Начало аренды</label>
                  <TextInput
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :value="inputValue.start"
                    v-on="inputEvents.start"
                    placeholder="Дата начала"
                  />
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Окончание аренды</label>
                  <TextInput
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :value="inputValue.end"
                    v-on="inputEvents.end"
                    placeholder="Дата окончания"
                  />
                </div>
              </div>
            </template>
          </DatePicker>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Цена за день:</span>
            <span class="font-medium">{{ currentPrice }} ₽</span>
          </div>
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Количество дней:</span>
            <span class="font-medium">{{ rentalDays }} {{ rentalDays === 1 ? 'день' : 'дней' }}</span>
          </div>
          <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between items-center">
            <span class="text-lg font-semibold">Итого:</span>
            <span class="text-2xl font-bold text-primary"> {{ formatPrice(rentalPrice) }} ₽ </span>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-3 mt-6">
          <Button
            variant="outline"
            @click="closeRentalModal"
            class="px-6 py-2.5 border-gray-300 text-gray-700 hover:bg-gray-50"
          >
            Отмена
          </Button>
          <Button
            @click="confirmRental"
            :disabled="isLoading"
            class="px-6 py-2.5 bg-primary hover:bg-primary text-white flex items-center gap-2"
          >
            <svg v-if="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ isLoading ? 'Обработка...' : 'Подтвердить аренду' }}
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.3s,
    transform 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
