export default defineNuxtRouteMiddleware(() => {
  const userStore = useUserStore()
  const { isAuth, showAuthForm } = storeToRefs(userStore)

  if (!isAuth.value) {
    showAuthForm.value = true
    // if (from.path !== to.path) return navigateTo(from)
    return navigateTo('/')
  }
})
