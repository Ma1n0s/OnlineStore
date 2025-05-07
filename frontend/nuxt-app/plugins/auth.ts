// Импортируем необходимые зависимости
import { useApi } from '~/composables/useApi'
import type { User } from '~/types/user.types'

export default defineNuxtPlugin(async nuxtApp => {
  // Оборачиваем логику в функцию, которая будет вызвана после инициализации приложения
  nuxtApp.hook('app:created', async () => {
    try {
      const { fetchUser, getCsrfToken } = useApi()
      const userStore = useUserStore()

      // Сначала получаем CSRF токен
      await getCsrfToken()

      // Затем запрашиваем данные пользователя
      const userData = await fetchUser()

      // Проверяем, что данные не пустые и соответствуют ожидаемой структуре
      if (userData && typeof userData === 'object' && 'id' in userData) {
        userStore.setUser(userData as User)
      }
    } catch (error) {
      console.error('Error loading user data:', error)
    }
  })

  return {
    provide: {
      // Предоставляем функции API через provide для использования в компонентах
      api: useApi(),
    },
  }
})
