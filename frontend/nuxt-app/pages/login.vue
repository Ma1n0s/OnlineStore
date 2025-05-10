<script setup>
const auth = useAuthStore()
const router = useRouter()

const form = reactive({
  email: '',
  phone: '',
  password: '',
  error: null
})

const login = async () => {
  try {
    form.error = null
    await auth.login({
      email: form.email || undefined,
      phone: form.phone || undefined,
      password: form.password
    })
    router.push('/account')
  } catch (error) {
    form.error = error.data?.message || 'Ошибка входа'
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Вход в аккаунт
        </h2>
      </div>
      
      <div v-if="form.error" class="bg-red-50 text-red-700 p-4 rounded-lg">
        {{ form.error }}
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="login">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="email" class="sr-only">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
              placeholder="Email"
            />
          </div>
          <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
              <div class="w-full border-t border-gray-300" />
            </div>
            <div class="relative flex justify-center">
              <span class="px-2 bg-white text-sm text-gray-500"> или </span>
            </div>
          </div>
          <div>
            <label for="phone" class="sr-only">Телефон</label>
            <input
              id="phone"
              v-model="form.phone"
              type="tel"
              autocomplete="tel"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
              placeholder="Телефон"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Пароль</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="current-password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
              placeholder="Пароль"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          >
            Войти
          </button>
        </div>
      </form>
    </div>
  </div>
</template>