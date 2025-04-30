<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import OrderSummary from '~/components/СartСheckout/OrderSummary.vue'
import CartHeader from '~/components/СartСheckout/CartHeader.vue'
import CartItemsList from '~/components/СartСheckout/CartItemsList.vue'
import RecipientData from '~/components/СartСheckout/RecipientData.vue'

useHead({
  title: 'Корзина | Абсолют техно',
  meta: [
    {
      name: 'description',
      content: 'Оформление заказа в Абсолют техно'
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
  items: [],
  customer: {
    name: 'Иван Иванович Иванов',
    phone: '+7 922 555 99-00',
  },
  deliveryAddress: '',
  paymentMethod: 'cash',
  showQrCode: false,
})

const loading = ref(true)

const fetchCartItems = async () => {
  try {
    const { data, error } = await useFetch('http://127.0.0.1:8000/api/cart')
    
    if (error.value) {
      console.error('Error fetching cart items:', error.value)
    } else {
      state.value.items = data.value.cartItems.map(item => ({
        id: item.id,
        name: item.product.name,
        code: item.product.code,
        description: item.product.description,
        price: item.product.price,
        quantity: item.quantity,
        selected: false,
        image: item.product.images?.[0]?.src || '',
        rentalType: item.options?.rental_days ? 'short-term' : null,
        rentalDays: item.options?.rental_days || null,
        rentalStart: item.options?.rental_start || null,
        rentalEnd: item.options?.rental_end || null,
        isRented: !!item.options?.rental_days
      }))
    }
  } catch (err) {
    console.error('Exception when fetching cart items:', err)
  } finally {
    loading.value = false
  }
}


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

const handleSearch = searchTerm => {
  // Реализация поиска по товарам
  console.log('Search term:', searchTerm)
}

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

const increaseRentalDays = item => {
  item.rentalDays += 1
}

const decreaseRentalDays = item => {
  if (item.rentalDays > 1) {
    item.rentalDays -= 1
  }
}

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

onMounted(fetchCartItems)
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

          <div v-if="!isEmptyCart">
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
