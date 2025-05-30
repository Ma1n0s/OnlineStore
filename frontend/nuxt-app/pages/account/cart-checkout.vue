<script setup>
import { ref, computed } from 'vue'
import OrderSummary from '~/components/СartСheckout/OrderSummary.vue'
import CartHeader from '~/components/СartСheckout/CartHeader.vue'
import CartItemsList from '~/components/СartСheckout/CartItemsList.vue'
import RecipientData from '~/components/СartСheckout/RecipientData.vue'
import { useCartStore } from '~/stores/cart'

const cartStore = useCartStore()
const cart = computed(() => cartStore?.cart)
const products = computed(() => cart?.products || [])

console.log(cart.value)

useHead({
  title: 'Корзина | Абсолют техно',
  meta: [
    {
      name: 'description',
      content: 'Оформление заказа в Абсолют техно',
    },
  ],
})

const loading = ref(false)
const showSuccessMessage = ref(false)
const showErrorMessage = ref(false)
const messageText = ref('')

const state = ref({
  showSecondForm: false,
  secondCustomer: {
    name: '',
    phone: '',
  },
  selectAll: false,
  items: cart.value?.products || [],
  customer: {
    name: '',
    phone: '',
  },
  deliveryAddress: '',
  paymentMethod: 'cash',
})

// const showMessage = (message, isError = false) => {
//   messageText.value = message
//   if (isError) {
//     showErrorMessage.value = true
//   } else {
//     showSuccessMessage.value = true
//   }
//   setTimeout(() => {
//     showSuccessMessage.value = false
//     showErrorMessage.value = false
//   }, 3000)
// }

// const fetchUserData = async () => {
//   try {
//     const { data } = await useFetch('http://127.0.0.1:8000/api/user', {
//       headers: {
//         Authorization: `Bearer ${useAuthToken().value}`,
//       },
//     })

//     if (data.value) {
//       state.value.customer = {
//         name: data.value.name || '',
//         phone: data.value.phone || '',
//       }
//     }
//   } catch (error) {
//     console.error('Error fetching user data:', error)
//   }
// }

// const fetchCartItems = async () => {
//   try {
//     const { data, error } = await useFetch('http://127.0.0.1:8000/api/cart', {
//       headers: {
//         Authorization: `Bearer ${useAuthToken().value}`,
//       },
//     })

//     if (error.value) {
//       throw error.value
//     }

//     state.value.items = data.value.cartItems.map(item => ({
//       id: item.id,
//       name: item.product.name,
//       code: item.product.code,
//       description: item.product.description,
//       price: item.product.price.final,
//       quantity: item.quantity,
//       selected: false,
//       image: item.product.images?.[0]?.src || '/images/Logo.png',
//       rentalType: item.options?.rental_days ? 'short-term' : null,
//       rentalDays: item.options?.rental_days || null,
//       rentalStart: item.options?.rental_start || null,
//       rentalEnd: item.options?.rental_end || null,
//       isRented: !!item.options?.rental_days,
//     }))
//   } catch (error) {
//     console.error('Error fetching cart items:', error)
//     if (error.statusCode === 401) {
//       navigateTo('/login')
//     }
//   } finally {
//     loading.value = false
//   }
// }

// const removeSelectedItems = async () => {
//   try {
//     const selectedIds = selectedItems.value.map(item => item.id)

//     await Promise.all(
//       selectedIds.map(async id => {
//         await useFetch(`http://127.0.0.1:8000/api/cart/${id}`, {
//           method: 'DELETE',
//           headers: {
//             Authorization: `Bearer ${useAuthToken().value}`,
//           },
//         })
//       })
//     )

//     // await fetchCartItems()
//     showMessage('Товары удалены из корзины')
//   } catch (error) {
//     console.error('Error removing items:', error)
//     showMessage('Не удалось удалить товары', true)
//   }
// }

// const updateCartItem = async (item, newQuantity) => {
//   try {
//     await useFetch(`http://127.0.0.1:8000/api/cart/${item.id}`, {
//       method: 'PATCH',
//       body: {
//         quantity: newQuantity,
//       },
//       headers: {
//         Authorization: `Bearer ${useAuthToken().value}`,
//       },
//     })

//     // await fetchCartItems()
//   } catch (error) {
//     console.error('Error updating cart item:', error)
//     showMessage('Не удалось изменить количество', true)
//   }
// }

const increaseQuantity = item => {
  const newQuantity = item.quantity + 1
  updateCartItem(item, newQuantity)
}

const decreaseQuantity = item => {
  if (item.quantity > 1) {
    const newQuantity = item.quantity - 1
    updateCartItem(item, newQuantity)
  }
}

const increaseRentalDays = item => {
  item.rentalDays += 1
}

const decreaseRentalDays = item => {
  if (item.rentalDays > 1) {
    item.rentalDays -= 1
  }
}

const toggleSelectAll = () => {
  state.value.items.forEach(item => {
    item.selected = state.value.selectAll
  })
}

// const selectedItems = computed(() => state.value.items.filter(item => item.selected))
const totalItemsCount = computed(() => cart.value?.products.length)
const isEmptyCart = computed(() => cart.value?.products.length === 0 || true)

const totalAmount = computed(() => {
  return state.value.items.reduce((sum, item) => {
    if (item.rentalType === 'short-term') {
      return sum + item.price * (item.rentalDays || 1) * item.quantity
    }
    return sum + item.price * item.quantity
  }, 0)
})

const discountAmount = computed(() => {
  return Math.round(totalAmount.value * 0.1)
})

const finalAmount = computed(() => totalAmount.value - discountAmount.value)

const formattedValues = computed(() => ({
  total: totalAmount.value.toLocaleString('ru-RU') + ' ₽',
  discount: '-' + discountAmount.value.toLocaleString('ru-RU') + ' ₽',
  final: finalAmount.value.toLocaleString('ru-RU') + ' ₽',
  empty: '0 ₽',
}))

// onMounted(async () => {
//   await fetchUserData()
//   await fetchCartItems()
// })
</script>

<template>
  <div>
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

    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
      <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
        <div class="lg:w-3/4">
          <CartHeader />

          <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
          </div>

          <div v-else-if="isEmptyCart" class="bg-white rounded-xl p-6 mb-6 shadow-sm text-center">
            <div class="mx-auto max-w-md">
              <!-- <img src="" alt="Пустая корзина" class="mx-auto h-40 w-40" /> -->
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

          <template v-else>
            <CartItemsList
              :products="products"
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
          </template>
        </div>

        <div class="lg:w-1/4">
          <OrderSummary
            :isEmptyCart="isEmptyCart"
            :customer="state.customer"
            :paymentMethod="state.paymentMethod"
            :totalItemsCount="totalItemsCount"
            totalWeight="0"
            :formattedTotalAmount="formattedValues.total"
            :formattedDiscountAmount="formattedValues.discount"
            :formattedFinalAmount="formattedValues.final"
            :emptyCartFinalAmount="formattedValues.empty"
          />
        </div>
      </div>
    </div>
  </div>
</template>
