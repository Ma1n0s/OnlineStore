<script setup>
// import { ref } from 'vue'
import OrderSummary from '~/components/СartСheckout/OrderSummary.vue'
import CartHeader from '~/components/СartСheckout/CartHeader.vue'
import CartItemsList from '~/components/СartСheckout/CartItemsList.vue'
import RecipientData from '~/components/СartСheckout/RecipientData.vue'
import { useCartStore } from '~/stores/cart'
import { storeToRefs } from 'pinia'

definePageMeta({
  middleware: ['auth'],
})

useHead({
  title: 'Корзина | Абсолют техно',
  meta: [
    {
      name: 'description',
      content: 'Оформление заказа в Абсолют техно',
    },
  ],
})

const cartStore = useCartStore()
const { products, isLoading: loading } = storeToRefs(cartStore)
// const loading = ref(isLoading.value)

// Альтернативный вариант с await
onMounted(async () => {
  if (!cartStore.isInitialized) {
    await cartStore.initCart()
  } else {
    await cartStore.refetchCart()
  }
  // loading.value = false
  console.log('Cart products:', products.value)
})
</script>

<template>
  <div class="min-h-screen">
    <div class="mx-auto w-full max-w-screen-2xl px-8 space-y-8 py-8">
      <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
        <div class="lg:w-3/4">
          <CartHeader />

          <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
          </div>

          <div v-else-if="!loading && products.length === 0" class="bg-white rounded-xl p-6 mb-6 shadow-sm text-center">
            <div class="mx-auto max-w-md">
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
            <CartItemsList />
            <RecipientData />
          </template>
        </div>

        <div class="lg:w-1/4">
          <OrderSummary />
        </div>
      </div>
    </div>
  </div>
</template>
