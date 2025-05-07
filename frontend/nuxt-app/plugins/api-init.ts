export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  // Создаем глобальную конфигурацию запросов
  const apiFetch = $fetch.create({
    baseURL: config.public.sanctum.baseUrl,
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })

  // Предоставляем экземпляр $fetch через provide
  return {
    provide: {
      apiFetch,
    },
  }
})
