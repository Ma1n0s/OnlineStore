import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const { getCart, addProduct, clearCart } = useCart()
  const cart = ref(null)
  const isInitialized = ref(false)

  const initCart = async () => {
    if (isInitialized.value) return
    cart.value = await getCart()
    isInitialized.value = true
    console.log(cart.value, 'cart')
  }

  console.log(cart.value, 'cart')

  const updateBacket = async () => {
    cart.value = await getCart()
  }

  const addToCart = async product => {
    if (!isInitialized.value) await initCart()

    // console.log(cart.value.products, cart.value)
    // const hasInCart = cart.value.products.some(el => el.product.id === product.id)

    // if (hasInCart) return

    await addProduct(cart.value, product)
    await updateBacket()
  }

  const clearUserCart = async () => {
    await clearCart()
    await updateBacket()
  }

  initCart()

  return { cart, addToCart, clearUserCart, updateBacket }
})
