<script setup>
import Textarea from '~/components/ui/Textarea/Textarea.vue'
import { useCartStore } from '~/stores/cart'
import { storeToRefs } from 'pinia'

const cartStore = useCartStore()
const { cart } = storeToRefs(cartStore)

const debouncedUpdate = useDebounceFn(async data => {
  await cartStore.updateCartMessage(data.target.value)
}, 1000)
</script>
<template>
  <div class="w-full my-4">
    <Textarea
      v-model="cart.message"
      @input="debouncedUpdate"
      id="message"
      rows="3"
      placeholder="Ваше сообщение..."
    ></Textarea>
  </div>
</template>
