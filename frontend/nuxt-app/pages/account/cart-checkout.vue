<script setup>
import { ref, watch, computed } from 'vue'
// import TextInput from '~/components/ui/Inputs/TextInput.vue'
// import Button from '~/components/ui/Button/Button.vue'
import OrderSummary from '~/components/СartСheckout/OrderSummary.vue'
import CartHeader from '~/components/СartСheckout/CartHeader.vue'
import CartItemsList from '~/components/СartСheckout/CartItemsList.vue'
import RecipientData from '~/components/СartСheckout/RecipientData.vue'

useHead({
  title: 'Корзина | Абсолют техно',
  meta: [
    {
      name: 'description',
      content:
        'Оформление заказа в Абсолют техно. Выберите способ доставки и оплаты, укажите адрес и контактные данные. Быстрое и удобное оформление покупок с возможностью использования бонусов.',
    },
  ],
})

const state = ref({
  showSecondForm: false,
  secondCustomer: {
    name: '',
    phone: '',
  },
  selectAll: false,
  items: [
    // Regular purchase items
    {
      id: 1,
      name: 'Бесщеточная дрель-шуруповерт AEG BS18SBL-202C',
      code: '16313057',
      description: 'Бесщеточная аккумуляторная дрель-шуруповерт AEG BS18SBL-202C 4935472277',
      price: 25790,
      quantity: 1,
      selected: false,
      image: 'Categories/Instruments.png',
      rentalType: null,
    },
    {
      id: 2,
      name: 'Компактный перфоратор',
      code: '12345678',
      description: 'Компактный перфоратор с мощным двигателем',
      price: 15000,
      quantity: 2,
      selected: false,
      image: 'Categories/Instruments.png',
      rentalType: null,
    },

    {
      id: 3,
      name: 'Генератор FUBAG TI 6000',
      code: 'GEN001',
      description: 'Бензиновый генератор 5.5 кВт',
      price: 2500,
      quantity: 1,
      selected: false,
      image: 'Categories/Instruments.png',
      rentalType: 'short-term',
      rentalDays: 3,
      isRented: true,
    },
    {
      id: 4,
      name: 'Бетономешалка 160л',
      code: 'CONCRETE001',
      description: 'Бетономешалка электрическая 160 литров',
      price: 1200,
      quantity: 1,
      selected: false,
      image: 'Categories/Instruments.png',
      rentalType: 'short-term',
      rentalDays: 7,
      isRented: true,
    },
    {
      id: 5,
      name: 'Бетономешалка 160л',
      code: 'CONCRETE001',
      description: 'Бетономешалка электрическая 160 литров',
      price: 1200,
      quantity: 1,
      selected: false,
      image: 'Categories/Instruments.png',
      rentalType: 'short-term',
      rentalDays: 7,
      isRented: true,
    },
  ],
  customer: {
    name: 'Иван Иванович Иванов',
    phone: '+7 922 555 99-00',
  },
  deliveryAddress: '',
  paymentMethod: 'cash',
  showQrCode: false,
})

const selectedItems = computed(() => state.value.items.filter(item => item.selected))
const totalItemsCount = computed(() => selectedItems.value.length)
const totalWeight = computed(() => '4,8')
const isEmptyCart = computed(() => state.value.items.length === 0)

const totalAmount = computed(() => {
  return selectedItems.value.reduce((sum, item) => {
    if (item.rentalType === 'short-term') {
      return sum + item.price * item.rentalDays * item.quantity
    } else if (item.rentalType === 'long-term') {
      const basePrice = item.price * item.rentalMonths
      const discountedPrice = item.discount ? basePrice * (1 - item.discount / 100) : basePrice
      return sum + discountedPrice * item.quantity
    }
    return sum + item.price * item.quantity
  }, 0)
})

const formattedTotalAmount = computed(() => {
  return totalAmount.value.toLocaleString('ru-RU') + ' ₽'
})

const discountAmount = computed(() => {
  // Calculate discount only for non-rental items
  const nonRentalItems = selectedItems.value.filter(item => !item.rentalType)
  const rentalDiscounts = selectedItems.value
    .filter(item => item.rentalType === 'long-term' && item.discount)
    .reduce((sum, item) => sum + (item.price * item.rentalMonths * item.discount) / 100, 0)

  return Math.round(nonRentalItems.reduce((sum, item) => sum + item.price * item.quantity, 0) * 0.1) + rentalDiscounts
})

const finalAmount = computed(() => {
  return totalAmount.value - discountAmount.value
})

const formattedFinalAmount = computed(() => {
  return finalAmount.value.toLocaleString('ru-RU') + ' ₽'
})

const emptyCartFinalAmount = computed(() => {
  return '0 ₽'
})

const formattedDiscountAmount = computed(() => {
  return '-' + discountAmount.value.toLocaleString('ru-RU') + ' ₽'
})

const increaseQuantity = item => {
  item.quantity += 1
}

const decreaseQuantity = item => {
  if (item.quantity > 1) {
    item.quantity -= 1
  }
}

const toggleSelectAll = () => {
  state.value.items.forEach(item => {
    item.selected = state.value.selectAll
  })
}

const removeSelectedItems = () => {
  state.value.items = state.value.items.filter(item => !item.selected)
}

// const formatPrice = price => {
//   return price.toLocaleString('ru-RU') + ' ₽'
// }

watch(
  () => state.value.items.every(item => item.selected),
  allSelected => {
    state.value.selectAll = allSelected
  }
)

watch(
  selectedItems,
  newVal => {
    if (newVal.length === 0) {
      state.value.selectAll = false
    }
  },
  { deep: true }
)

const increaseRentalDays = item => {
  item.rentalDays += 1
}

const decreaseRentalDays = item => {
  if (item.rentalDays > 1) {
    item.rentalDays -= 1
  }
}

// const orderDate = computed(() => {
//   const today = new Date()
//   const options = { year: 'numeric', month: 'long', day: 'numeric' }
//   return today.toLocaleDateString('ru-RU', options)
// })
</script>

<template>
  <div>
    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-16 py-8">
      <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
        <div class="lg:w-3/4">
          <CartHeader :cart-number="state.cartNumber" @search="handleSearch" />

          <div v-if="isEmptyCart" class="bg-white rounded-xl p-6 mb-6 shadow-sm text-center">
            <div class="mx-auto max-w-md">
              <img src="" alt="" class="" />
              <h2 class="mt-3 sm:mt-4 text-lg sm:text-xl font-bold text-gray-800">Ваша корзина пока пуста</h2>
              <p class="mt-2 text-sm sm:text-base text-gray-600">
                Акции и специальные предложения помогут вам определиться с выбором!
              </p>
              <NuxtLink
                to="/"
                class="mt-4 sm:mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-hover"
              >
                Перейти к покупкам
              </NuxtLink>
            </div>
          </div>

          <CartItemsList
            v-else
            :items="state.items"
            :select-all="state.selectAll"
            @update:selectAll="val => (state.selectAll = val)"
            :total-items-count="totalItemsCount"
            @toggleSelectAll="toggleSelectAll"
            @removeSelectedItems="removeSelectedItems"
            @increaseQuantity="increaseQuantity"
            @decreaseQuantity="decreaseQuantity"
            @increaseRentalDays="increaseRentalDays"
            @decreaseRentalDays="decreaseRentalDays"
          />

          <div v-if="!isEmptyCart" class="lg:w-3/4">
            <RecipientData
              :customer="state.customer"
              :show-second-form="state.showSecondForm"
              :second-customer="state.secondCustomer"
              :delivery-address="state.deliveryAddress"
              :payment-method="state.paymentMethod"
              @update:showSecondForm="val => (state.showSecondForm = val)"
              @update:secondCustomer="val => (state.secondCustomer = val)"
              @update:deliveryAddress="val => (state.deliveryAddress = val)"
              @update:paymentMethod="val => (state.paymentMethod = val)"
            />
          </div>
        </div>

        <div class="lg:w-1/4">
          <OrderSummary
            :isEmptyCart="isEmptyCart"
            :customer="state.customer"
            :paymentMethod="state.paymentMethod"
            :totalItemsCount="totalItemsCount"
            :totalWeight="totalWeight"
            :formattedTotalAmount="formattedTotalAmount"
            :formattedDiscountAmount="formattedDiscountAmount"
            :formattedFinalAmount="formattedFinalAmount"
            :emptyCartFinalAmount="emptyCartFinalAmount"
          />
        </div>
      </div>
    </div>
  </div>
</template>
