import { defineStore } from 'pinia'
import type { User } from '~/types/user.types'

export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null)
  const isAuth = ref(false)

  const setUser = (userData: User) => {
    user.value = userData
    isAuth.value = true
  }

  const clearUser = () => {
    user.value = null
    isAuth.value = false
  }

  const fetchUser = async () => {
    try {
      const { data } = await useSanctumFetch('/api/user', {
        credentials: 'include',
      })

      if (data.value) {
        user.value = data.value
        isAuth.value = true
        console.log('User data set:', user.value); 
      }
    } catch (error) {
      console.error('Error fetching user:', error)
      user.value = null
      isAuth.value = false
    }
  }

  return { user, isAuth, fetchUser, setUser, clearUser }
})
