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

    data.value.selected = !!data.value.selected

    data.value.products.forEach(product => {
      product.orderPrice = product.pivot.price_at_order
      product.orderQuantity = product.pivot.quantity
      product.selected = product.pivot.selected === 1
    })

    return data
  }

  const addProduct = async (cart, product) => {
    const dataCart = {
      order_id: cart.value.id,
      product_id: product.id,
      quantity: product.quantity,
      price_at_order: product.price,
    }

    try {
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
    } catch (e) {
      console.log(e)
    }
  }

  const removeSelectedOrderProducts = async cart => {
    try {
      await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products/selected`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
      })
    } catch (e) {
      console.log(e)
    }
  }

  const removeOrderProducts = async (cart, product) => {
    try {
      await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products/${product.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
      })
    } catch (e) {
      console.log(e)
    }
  }

  const updateMessage = async (cart, message) => {
    try {
      return await $fetch(`${backendUrl}/api/orders/${cart.value.id}/message`, {
        method: 'POST',
        body: {
          message,
        },
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
      })
    } catch (e) {
      console.log(e)
    }
  }

  const updateOrderProduct = async (cart, product) => {
    try {
      return await $fetch(`${backendUrl}/api/orders/${cart.value.id}/products/${product.id}`, {
        method: 'PUT',
        body: {
          quantity: product.orderQuantity,
          selected: product.pivot.selected,
        },
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
      })
    } catch (e) {
      console.log(e)
    }
  }

  const updateSelected = async (cart, selected: boolean, all = true) => {
    try {
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
    } catch (e) {
      console.log(e)
    }
  }

  const changeBonus = async bonus => {
    try {
      return await $fetch(`${backendUrl}/api/orders/use-bonus`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
        body: {
          checkBonus: bonus,
        },
      })
    } catch (e) {
      console.log(e)
    }
  }

  const completeOrder = async () => {
    try {
      return await $fetch(`${backendUrl}/api/orders/create-order`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value,
        },
        credentials: 'include',
      })
    } catch (e) {
      console.log(e)
    }
  }

  return {
    getCart,
    addProduct,
    removeSelectedOrderProducts,
    removeOrderProducts,
    updateOrderProduct,
    completeOrder,
    updateSelected,
    changeBonus,
    updateMessage,
  }
}
