import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const {
    getCart,
    addProduct,
    updateSelected,
    completeOrder,
    updateOrderProduct,
    removeSelectedOrderProducts,
    removeOrderProducts,
    changeBonus,
  } = useCart()
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

  const refetchCart = async () => {
    try {
      const response = await getCart()
      cart.value = response.value
      products.value = response.value.products || []
      console.log(cart.value.selected)
      console.log(products.value)
    } catch (error) {
      console.error('Failed to initialize cart:', error)
    }
  }

  const addToCart = async product => {
    if (!isInitialized.value) await initCart()
    await addProduct(cart, product)
    await refetchCart()
  }

  const setSelected = async selected => {
    await updateSelected(cart, selected)
    cart.value.selected = selected
    await refetchCart()
  }

  const updateProduct = async product => {
    await updateOrderProduct(cart, product)
  }

  const createOrder = async () => {
    await completeOrder()
    await refetchCart()
  }

  const checkProductInCart = val => {
    return products.value.some(product => product.id === val.id)
  }

  const removeSelected = async () => {
    await removeSelectedOrderProducts(cart)
    await refetchCart()
  }

  const remove = async product => {
    await removeOrderProducts(cart, product)
    await refetchCart()
  }

  const updateOrderBonus = async bonus => {
    await changeBonus(bonus)
  }

  // Инициализируем корзину при создании хранилища
  initCart()

  return {
    cart,
    products,
    isLoading,
    isInitialized,
    removeSelected,
    remove,
    checkProductInCart,
    addToCart,
    refetchCart,
    setSelected,
    updateProduct,
    createOrder,
    updateOrderBonus,
    initCart, // Добавляем возможность повторной инициализации
  }
})
