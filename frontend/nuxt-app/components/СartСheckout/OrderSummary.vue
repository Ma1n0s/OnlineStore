<script setup>
import Button from '~/components/ui/Button/Button.vue'
import { useCartStore } from '~/stores/cart'

const cartStore = useCartStore()
const { cart, products } = storeToRefs(cartStore)

const sendOrder = useDebounceFn(async () => {
  try {
    await cartStore.createOrder()
  } catch (e) {
    console.log(e)
  }
}, 1000)

const isEmpty = computed(() => products.value.length === 0)
const isSelected = computed(() => products.value.some(product => product.selected))
const isNotEnough = computed(() =>
  products.value.filter(product => product.type === 'instock').some(product => product.quantity < product.orderQuantity)
)
console.log(!isNotEnough.value, 'enough')

const weight = computed(() => {
  const result = products.value.reduce(
    (acc, val) => (val.selected ? acc + val?.weight * val?.orderQuantity : acc + 0),
    0
  )
  return result ? result : 0
})

const sum = computed(() => {
  const result = products.value.reduce(
    (acc, val) => (val.selected ? acc + val?.price * val?.orderQuantity : acc + 0),
    0
  )
  return result
})

const formatDateTime = datatime => {
  const date = new Date(datatime)
  return new Intl.DateTimeFormat('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  })
    .format(date)
    .replace(',', '')
}

console.log(isEmpty.value, isSelected.value)

// const orderDate = computed(() => {
//   const today = new Date()
//   const options = { year: 'numeric', month: 'long', day: 'numeric' }
//   return today.toLocaleDateString('ru-RU', options)
// })
</script>
<template>
  <div class="bg-white rounded-xl p-4 shadow-2xl sm:p-6 sticky top-8 sm:top-20">
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-if="isEmpty">Ваш заказ</h2>
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-else>Оформление</h2>

    <div v-if="isEmpty" class="mb-3 sm:mb-4">
      <p class="text-xs sm:text-sm text-gray-600">Выберите товары для оформления заказа</p>
    </div>

    <div v-else class="mb-3 sm:mb-4 space-y-2 sm:space-y-3">
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Дата заказа</p>
        <p class="text-sm sm:text-base font-medium">{{ formatDateTime(cart.updated_at) }}</p>
      </div>
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Покупатель</p>
        <p class="text-sm sm:text-base font-medium" v-if="cart.user">{{ cart.user.name }}</p>
        <p class="text-xs sm:text-sm text-gray-400 italic" v-else>Не указано</p>
      </div>
    </div>

    <div v-if="!isEmpty && isSelected" class="flex items-center justify-between mb-3 sm:mb-4">
      <h2 class="text-sm sm:text-base font-bold text-gray-800">
        {{ products.reduce((acc, val) => (val.selected ? acc + 1 : acc + 0), 0) }} товар
        <span v-if="weight">• {{ weight }} кг</span>
      </h2>
    </div>

    <div v-if="!isEmpty && isSelected" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
      <div class="flex justify-between items-center text-sm sm:text-base">
        <span>Сумма</span>
        <span class="font-medium text-gray-800">{{ sum }} ₽</span>
      </div>
    </div>

    <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-3 sm:mb-4">
      <div class="flex justify-between items-center mb-4 sm:mb-6">
        <span class="text-base sm:text-lg font-bold text-gray-800">Итого</span>
        <span class="text-xl sm:text-2xl font-bold text-gray-800"> {{ isEmpty ? 0 : sum }} ₽ </span>
      </div>
    </div>

    <Button
      variant="primary"
      size="medium"
      :disabled="isEmpty || !isSelected || isNotEnough"
      class="w-full shadow-md text-sm sm:text-base"
      type="submit"
      @click="sendOrder"
    >
      Оформить заказ
    </Button>

    <div class="mt-2 text-xxs sm:text-xs text-gray-500 text-center">
      Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных
    </div>
  </div>
</template>
