import { defineStore } from 'pinia'
import type { User } from '~/types/user.types'

export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null)
  const isAuth = ref(false)

  const showAuthForm = ref(false)

  const setUser = (userData: User) => {
    user.value = userData
    isAuth.value = true
  }

  const clearUser = () => {
    user.value = null
    isAuth.value = false
  }

  const loadCsrfToken = async () => {
    // await $fetch(`${backendUrl}/sanctum/csrf-cookie`, {
    //   credentials: 'include',
    // })

    await useSanctumFetch('/sanctum/csrf-cookie', {
      method: 'GET',
      credentials: 'include',
    })

    const token = useCookie('XSRF-TOKEN')
    console.log('csrf token is setted', token)
  }

  loadCsrfToken()

  const fetchUser = async () => {
    try {
      const { data } = await useSanctumFetch('/api/user', {
        credentials: 'include',
      })

      if (data.value) {
        console.log('Full API response:', data.value)
        console.log('Orders in response:', data.value.orders)

        user.value = {
          ...data.value,
          // ...data.value.profile,
          companyDetails: {
            name: data.value.profile?.company_name,
            inn: data.value.profile?.inn,
            kpp: data.value.profile?.kpp,
            address: data.value.profile?.legal_address,
            director: data.value.profile?.director,
            phone: data.value.profile?.company_phone,
            email: data.value.profile?.company_email,
          },
          bonusBalance: data.value.bonus_balance,
          bonusTransactions: data.value.bonus_transactions || [],
          orders: data.value.orders || [],
          phone: data.value.phone,
        }
        isAuth.value = true
        console.log('User data after processing:', user.value)
        console.log('Processed orders:', user.value.orders)
      }
    } catch (error) {
      console.error('Error fetching user:', error)
      user.value = null
      isAuth.value = false
    }
  }

  return { user, isAuth, showAuthForm, fetchUser, setUser, clearUser }
})
