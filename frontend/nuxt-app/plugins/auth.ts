export default defineNuxtPlugin(async () => {
  try {
    const userStore = useUserStore()
    await userStore.fetchUser()
  } catch (e) {
    console.log(e)
  }
})
