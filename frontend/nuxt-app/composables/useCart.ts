export const useCart = () => {
  const {
    public: { backendUrl },
  } = useRuntimeConfig()

  const getCart = async () => {
    const data = ref({})
    data.value = await $fetch(`${backendUrl}/api/orders/active-cart`, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      server: false,
    })

    data.value.products.forEach(product => {
      product.orderPrice = product.pivot.price_at_order
      product.orderQuantity = product.pivot.quantity
      product.selected = product.pivot.selected === 1
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

  const updateOrderProduct = async (cart, product) => {
    return await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products/${product.id}`, {
      method: 'PUT',
      body: {
        quantity: product.orderQuantity,
        selected: product.selected,
      },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })
  }

  const clearCart = async () => {}

  const updateSelected = async (cart, selected: boolean, all = true) => {
    return await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products/selected`, {
      method: 'POST',
      body: { selected, all },
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })
  }

  const completeOrder = async () => {
    return await $fetch(`${backendUrl}/api/orders/create-order`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
      },
      credentials: 'include',
    })
  }

  return {
    getCart,
    addProduct,
    removeProduct,
    updateOrderProduct,
    clearCart,
    completeOrder,
    updateSelected,
  }
}
