<script setup>
import { ref } from 'vue'
import TextInput from '../ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
import { DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const isRentalModalOpen = ref(false)
const rentalDays = ref(1)
const rentalPrice = ref(props.product.price.final)
const showSuccessMessage = ref(false)
const showErrorMessage = ref(false)
const messageText = ref('')
const isLoading = ref(false)

const dateRange = ref({
  start: new Date(),
  end: new Date(new Date().setDate(new Date().getDate() + 1)),
})

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
}

const closeRentalModal = () => {
  isRentalModalOpen.value = false
}

const calculateRentalPrice = () => {
  if (dateRange.value.start && dateRange.value.end) {
    const diffTime = Math.abs(dateRange.value.end.getTime() - dateRange.value.start.getTime())
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    rentalDays.value = diffDays > 0 ? diffDays : 1
    rentalPrice.value = props.product.price.final * rentalDays.value
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
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
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
  <div class="space-y-6">
    <div v-if="showSuccessMessage" class="fixed top-4 right-4 z-50">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ messageText }}
      </div>
    </div>
    <div v-if="showErrorMessage" class="fixed top-4 right-4 z-50">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {{ messageText }}
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex flex-col gap-4">
        <div>
          <h2 class="text-xl font-bold text-second mb-2">Цена товара</h2>
          <div class="flex items-center gap-4">
            <p class="text-3xl font-bold">{{ product.price.final }} ₽</p>
            <p
              v-if="product.price.original && product.price.original !== product.price.final"
              class="text-lg line-through text-gray"
            >
              {{ product.price.original }} ₽
            </p>
            <p v-if="product.price.savings" class="text-primary font-medium">
              Экономия {{ product.price.savings }} ₽
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <Button
            @click="addToCart"
            :disabled="isLoading"
            class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition"
          >
            <span v-if="!isLoading">В корзину</span>
            <span v-else>Добавление...</span>
          </Button>
          <Button
            @click="openRentalModal"
            class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition"
          >
            В аренду
          </Button>
        </div>
      </div>
    </div>

    <Modal :isOpen="isRentalModalOpen" title="Аренда товара" @close="closeRentalModal">
      <div class="mt-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Выберите даты аренды</label>
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
              <div class="flex flex-col sm:flex-row gap-2">
                <div class="w-full">
                  <label class="block text-xs text-gray-500 mb-1">Начало</label>
                  <TextInput
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                    :value="inputValue.start"
                    v-on="inputEvents.start"
                    placeholder="Дата начала"
                  />
                </div>
                <div class="w-full">
                  <label class="block text-xs text-gray-500 mb-1">Конец</label>
                  <TextInput
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                    :value="inputValue.end"
                    v-on="inputEvents.end"
                    placeholder="Дата окончания"
                  />
                </div>
              </div>
            </template>
          </DatePicker>
        </div>

        <div class="pt-2">
          <p class="text-lg font-semibold">Итоговая стоимость:</p>
          <p class="text-2xl font-bold text-primary">{{ rentalPrice }} ₽</p>
          <p class="text-sm text-gray-500">
            Цена за день: {{ product.price.final }} ₽ × {{ rentalDays }} {{ rentalDays === 1 ? 'день' : 'дней' }}
          </p>
        </div>
      </div>

      <template #footer">
        <div class="flex justify-end gap-3 mt-6">
          <Button variant="outline" @click="closeRentalModal">Отмена</Button>
          <Button @click="confirmRental" :disabled="isLoading">
            <span v-if="!isLoading">Подтвердить аренду</span>
            <span v-else>Обработка...</span>
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>