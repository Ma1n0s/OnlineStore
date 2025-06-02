export const useCart = () => {
  const {
    public: { backendUrl },
  } = useRuntimeConfig()

  const getCart = async () => {
    const data = ref({})
    data.value = await $fetch(`${backendUrl}/api/orders/active-cart`, {
      method: 'GET',
      credentials: 'include',
      server: false,
    })

    return data
  }

  //   'order_id',
  // 'product_id',
  // 'quantity',
  // 'price_at_order'

  const addProduct = async (cart, product) => {
    console.log(cart.value.id)
    // await $fetch(`${backendUrl}/sanctum/csrf-cookie`, {
    //   credentials: 'include',
    // })

    const dataCart = {
      order_id: cart.value.id,
      product_id: product.id,
      quantity: product.quantity,
      price_at_order: product.price,
    }

    // const { data } = await useSanctumFetch(`/api/orders/${cart.value.id}/products`, {
    //   method: 'POST',
    //   headers: {
    //     'Content-Type': 'application/json',
    //     Accept: 'application/json',
    //     'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
    //   },
    //   credentials: 'include',
    //   body: JSON.stringify(dataCart),
    // })

    // console.log(data)

    await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products`, {
      method: 'POST',
      body: dataCart,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })
  }

  const removeProduct = async () => {}

  const updateProduct = async () => {}

  const clearCart = async () => {}

  return { getCart, addProduct, removeProduct, updateProduct, clearCart }
}
