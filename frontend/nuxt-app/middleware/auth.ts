export default defineNuxtRouteMiddleware(() => {
  const userStore = useUserStore()
  const { isAuth, showAuthForm } = storeToRefs(userStore)

  if (!isAuth.value) {
    showAuthForm.value = true
    return navigateTo('/') // или куда вам нужно перенаправить
  }
})
