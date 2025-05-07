export default defineNuxtPlugin(() => {
  const router = useRouter()

  router.beforeEach((to, from, next) => {
    // Если путь начинается с /api или /sanctum, не обрабатывать его как маршрут Vue
    if (to.path.startsWith('/api/') || to.path.startsWith('/sanctum/')) {
      return false // Отменяем обработку роутера для API-запросов
    }
    return next()
  })
})
