import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', () => {
  const cart = ref([])

  const addToCart = newCart => {
    cart.value.push(newCart)
  }

  const clearCart = () => {
    cart.value = []
  }

  return { cart, addToCart, clearCart }
})
