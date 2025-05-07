export const useApi = () => {
  // Проверка на доступность контекста Nuxt
  // Нужно использовать try/catch, так как при импорте из плагина
  // функция может быть вызвана до инициализации Nuxt
  let config: any
  let baseURL: string

  try {
    config = useRuntimeConfig()
    baseURL = config.public.sanctum.baseUrl
  } catch (error) {
    console.error('Error getting runtime config:', error)
    // Используем значения по умолчанию, если контекст Nuxt недоступен
    baseURL = 'http://127.0.0.1:8000'
  }

  const defaultHeaders = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  }

  // Безопасная функция для получения cookie
  const getXsrfToken = () => {
    try {
      const token = useCookie('XSRF-TOKEN')
      return token.value || ''
    } catch (error) {
      console.error('Error getting XSRF-TOKEN:', error)
      return ''
    }
  }

  // Обновить CSRF токен
  const refreshCsrfToken = async () => {
    try {
      // Делаем запрос для обновления CSRF токена
      await $fetch('/sanctum/csrf-cookie', {
        baseURL,
        credentials: 'include',
        method: 'GET',
        headers: {
          ...defaultHeaders,
          Origin: 'http://127.0.0.1:3000',
        },
      })
      return getXsrfToken()
    } catch (error) {
      console.error('Error refreshing CSRF token:', error)
      throw error
    }
  }

  /**
   * Получение данных пользователя
   */
  const fetchUser = async () => {
    try {
      // Сначала обновляем CSRF токен
      await refreshCsrfToken()

      // Затем делаем запрос с токеном
      return await $fetch('/api/user', {
        baseURL,
        credentials: 'include',
        method: 'GET',
        headers: {
          ...defaultHeaders,
          'X-XSRF-TOKEN': getXsrfToken(),
          Origin: 'http://127.0.0.1:3000',
        },
      })
    } catch (error) {
      console.error('Error fetching user data:', error)
      throw error
    }
  }

  /**
   * Получение CSRF токена
   */
  const getCsrfToken = async () => {
    return await refreshCsrfToken()
  }

  /**
   * Получение категорий
   */
  const fetchCategories = async () => {
    try {
      // Обновляем CSRF токен перед запросом
      await refreshCsrfToken()

      return await $fetch('/api/categories', {
        baseURL,
        credentials: 'include',
        headers: {
          ...defaultHeaders,
          'X-XSRF-TOKEN': getXsrfToken(),
          Origin: 'http://127.0.0.1:3000',
        },
      })
    } catch (error) {
      console.error('Error fetching categories:', error)
      throw error
    }
  }

  /**
   * Универсальный вызов API
   */
  const callApi = async (endpoint: string, options = {}) => {
    try {
      // Обновляем CSRF токен перед запросом, если это не запрос на получение токена
      if (!endpoint.includes('/sanctum/csrf-cookie')) {
        await refreshCsrfToken()
      }

      const headers: Record<string, string> = {
        ...defaultHeaders,
        Origin: 'http://127.0.0.1:3000',
      }

      // Добавляем XSRF-TOKEN, если он есть
      const token = getXsrfToken()
      if (token) {
        headers['X-XSRF-TOKEN'] = token
      }

      return await $fetch(endpoint, {
        baseURL,
        credentials: 'include',
        headers,
        ...options,
      })
    } catch (error) {
      console.error(`API Error (${endpoint}):`, error)
      throw error
    }
  }

  return {
    fetchUser,
    fetchCategories,
    getCsrfToken,
    callApi,
  }
}
