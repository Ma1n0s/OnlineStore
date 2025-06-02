import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const { getCart, addProduct, clearCart } = useCart()
  const cart = ref<Record<any, any> | null>(null)
  const products = ref<Array<any>>([]) // Используем ref вместо reactive
  const isInitialized = ref(false)
  const isLoading = ref(false)

  const initCart = async () => {
    if (isInitialized.value || isLoading.value) return

    isLoading.value = true
    try {
      const response = await getCart()
      console.log(response.value, 'wait what')
      cart.value = response.value
      products.value = response.value.products || []
      isInitialized.value = true
    } catch (error) {
      console.error('Failed to initialize cart:', error)
    } finally {
      isLoading.value = false
    }
  }

  const updateCart = async () => {
    isLoading.value = true
    try {
      const response = await getCart()
      cart.value = response || {}
      products.value = response.products || []
    } catch (error) {
      console.error('Failed to update cart:', error)
    } finally {
      isLoading.value = false
    }
  }

  const addToCart = async product => {
    if (!isInitialized.value) await initCart()
    await addProduct(cart, product)
    await updateCart()
  }

  const clearUserCart = async () => {
    await clearCart()
    await updateCart()
  }

  // Инициализируем корзину при создании хранилища
  initCart()

  return {
    cart,
    products,
    isLoading,
    isInitialized,
    addToCart,
    clearUserCart,
    updateCart,
    initCart, // Добавляем возможность повторной инициализации
  }
})
