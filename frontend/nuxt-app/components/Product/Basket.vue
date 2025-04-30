<script setup lang="ts">
import { ref } from 'vue'
import TextInput from '../ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
import { DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'

import type { Product } from '~/types/product.types'
const { product } = defineProps<{
  product: Product
}>()

const isRentalModalOpen = ref(false)
const rentalDays = ref(1)
const rentalPrice = ref(product.price.final)

const dateRange = ref({
  start: new Date(),
  end: new Date(new Date().setDate(new Date().getDate() + 1)),
})

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
    rentalPrice.value = product.price.final * rentalDays.value
  }
}

const confirmRental = () => {
  addToCart({
    rental_days: rentalDays.value,
    rental_start: dateRange.value.start,
    rental_end: dateRange.value.end,
    rental_price: rentalPrice.value,
  })
  closeRentalModal()
}

const addToCart = async (options = {}) => {
  try {
    const { data, error } = await useFetch('http://127.0.0.1:8000/api/cart', {
      method: 'POST',
      body: {
        product_id: product.id,
        quantity: 1,
        options: options,
      },
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    })

    if (error.value) {
      console.error('Error adding to cart:', error.value)
      // Показать сообщение об ошибке
    } else {
      console.log('Product added to cart:', data.value)
      // Показать уведомление об успешном добавлении
    }
  } catch (err) {
    console.error('Exception when adding to cart:', err)
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Блок с кнопками -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="flex flex-col gap-4">
        <div>
          <h2 class="text-xl font-bold text-second mb-2">Сезон впереди</h2>
          <div class="flex items-center gap-4">
            <p class="text-3xl font-bold">{{ product.price }} ₽</p>
            <p
              v-if="product.price.original && product.price.original !== product.price.final"
              class="text-lg line-through text-gray"
            >
              {{ product.price.original }} ₽
            </p>
            <p v-if="product.price.savings" class="text-primary font-medium">Экономия {{ product.price.savings }} ₽</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <button
            v-if="product"
            @click="addToCart"
            class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition"
          >
            В корзину
          </button>
          <button
            v-if="product"
            @click="openRentalModal"
            class="bg-primary hover:bg-second-hover text-white py-3 px-6 rounded-lg font-medium transition"
          >
            В аренду
          </button>
        </div>
      </div>
    </div>

    <!-- <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-bold mb-4">Основные характеристики</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
          <div>
            <ul class="space-y-3">
              <li v-for="value in product.mainSpecifications" :key="value.name" class="flex justify-between">
                <span class="text-gray-500">{{ value.name }}</span>
                <span class="font-medium">{{ value.value }}</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="space-y-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-2">Экран</h3>
            <ul class="space-y-3">
              <li
                v-for="(value, key) in product.specifications['Вторичные характеристики']"
                :key="key"
                class="flex justify-between"
              >
                <span class="text-gray-500">{{ key.replace(' ', ' ') }}</span>
                <span class="font-medium">{{ value }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div> -->
  </div>

  <Modal :isOpen="isRentalModalOpen" title="Аренда товара" @close="closeRentalModal" @confirm="confirmRental">
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

    <template #footer>
      <div class="flex justify-end gap-3 mt-6">
        <Button variant="outline" @click="closeRentalModal">Отмена</Button>
        <Button @click="confirmRental">Подтвердить аренду</Button>
      </div>
    </template>
  </Modal>
</template>
