<script setup>
import Button from '~/components/ui/Button/Button.vue'
defineProps({
  isEmptyCart: {
    type: Boolean,
    required: true,
  },
  customer: {
    type: Object,
    required: true,
    default: () => ({ name: '', phone: '' }),
  },
  paymentMethod: {
    type: String,
    required: true,
    default: 'cash',
  },
  totalItemsCount: {
    type: Number,
    required: true,
    default: 0,
  },
  totalWeight: {
    type: String,
    required: true,
    default: '0',
  },
  formattedTotalAmount: {
    type: String,
    required: true,
    default: '0 ₽',
  },
  formattedDiscountAmount: {
    type: String,
    required: true,
    default: '0 ₽',
  },
  formattedFinalAmount: {
    type: String,
    required: true,
    default: '0 ₽',
  },
  emptyCartFinalAmount: {
    type: String,
    required: true,
    default: '0 ₽',
  },
})

const orderDate = computed(() => {
  const today = new Date()
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return today.toLocaleDateString('ru-RU', options)
})
</script>
<template>
  <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm sticky top-4 sm:top-6 border border-gray-200">
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-if="!isEmptyCart">Ваш заказ</h2>
    <h2 class="text-base sm:text-lg font-bold mb-3 sm:mb-4 text-gray-800" v-else>Оформление</h2>

    <div v-if="isEmptyCart" class="mb-3 sm:mb-4">
      <p class="text-xs sm:text-sm text-gray-600">Выберите товары для оформления заказа</p>
    </div>

    <div v-else class="mb-3 sm:mb-4 space-y-2 sm:space-y-3">
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Дата заказа</p>
        <p class="text-sm sm:text-base font-medium">{{ orderDate }}</p>
      </div>
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Покупатель</p>
        <p class="text-sm sm:text-base font-medium" v-if="customer.name">{{ customer.name }}</p>
        <p class="text-xs sm:text-sm text-gray-400 italic" v-else>Не указано</p>
      </div>
      <div>
        <p class="text-xs sm:text-sm text-gray-500">Способ оплаты</p>
        <p class="text-sm sm:text-base font-medium">
          <span v-if="paymentMethod === 'qr'">QR-код</span>
          <span v-else-if="paymentMethod === 'cash'">Наличные</span>
          <span v-else-if="paymentMethod === 'card'">Картой</span>
          <span v-else-if="paymentMethod === 'installment'">Рассрочка</span>
        </p>
      </div>
    </div>

    <div v-if="!isEmptyCart" class="flex items-center justify-between mb-3 sm:mb-4">
      <h2 class="text-sm sm:text-base font-bold text-gray-800">{{ totalItemsCount }} товар • {{ totalWeight }} кг</h2>
      <span class="text-xs sm:text-sm text-green-600 font-medium">Выгода</span>
    </div>

    <div v-if="!isEmptyCart" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
      <div class="flex justify-between items-center text-sm sm:text-base">
        <span>Сумма</span>
        <span class="font-medium text-gray-800">{{ formattedTotalAmount }}</span>
      </div>
      <div class="flex justify-between items-center text-sm sm:text-base">
        <span>Скидка</span>
        <span class="text-red-600">{{ formattedDiscountAmount }}</span>
      </div>
    </div>

    <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-3 sm:mb-4">
      <div class="flex justify-between items-center mb-4 sm:mb-6">
        <span class="text-base sm:text-lg font-bold text-gray-800">Итого</span>
        <span class="text-xl sm:text-2xl font-bold text-gray-800">
          {{ isEmptyCart ? emptyCartFinalAmount : formattedFinalAmount }}
        </span>
      </div>
    </div>

    <Button
      variant="primary"
      size="medium"
      :disabled="isEmptyCart || totalItemsCount === 0"
      class="w-full shadow-md text-sm sm:text-base"
      type="submit"
    >
      Оформить заказ
    </Button>

    <div class="mt-2 text-xxs sm:text-xs text-gray-500 text-center">
      Нажимая на кнопку, вы соглашаетесь с условиями обработки персональных данных
    </div>
  </div>
</template>
