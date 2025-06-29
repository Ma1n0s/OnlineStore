<script setup>
import { reactive } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'
import { emailRegex } from '~/shared/regexp'

const { setUser } = useUserStore()

const emit = defineEmits(['close'])

const form = reactive({
  email: '',
  password: '',
  rememberMe: false,
  isLoading: false,
  emailError: '',
  passwordError: '',
})

const validate = () => {
  let valid = true

  const isValidEmail = emailRegex.test(form.email)

  if (!form.email) {
    form.emailError = 'Пожалуйста, введите email'
    valid = false
  } else if (!isValidEmail) {
    form.emailError = 'Введите корректный email'
    valid = false
  } else {
    form.emailError = ''
  }

  if (!form.password) {
    form.passwordError = 'Пожалуйста, введите пароль'
    valid = false
  } else if (form.password.length < 6) {
    form.passwordError = 'Пароль должен быть не менее 6 символов'
    valid = false
  } else {
    form.passwordError = ''
  }

  return valid
}

const handleLogin = async () => {
  if (!validate()) return

  try {
    form.isLoading = true

    await useSanctumFetch('/sanctum/csrf-cookie', {
      method: 'GET',
      credentials: 'include',
    })

    const { data } = await useSanctumFetch('/api/auth/email-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: form.email,
        password: form.password,
      }),
    })

    if (data.value && data.value.status === 'success') {
      setUser(data.value.user)
      const { user } = useSanctumAuth()
      user.value = data.value.user
      emit('close')
    } else {
      form.passwordError = 'Неверный email или пароль'
    }
  } catch (error) {
    console.log('Login error:', error)
  } finally {
    form.isLoading = false
  }
}
</script>
<template>
  <div class="w-full space-y-4">
    <div class="space-y-1">
      <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
      <TextInput
        id="email"
        v-model="form.email"
        type="email"
        placeholder="example@mail.com"
        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:bg-white focus:border-transparent transition"
        :class="{ 'border-primary-active': form.emailError }"
      />
      <p v-if="form.emailError" class="text-primary text-xs mt-1">{{ form.emailError }}</p>
    </div>

    <div class="space-y-1">
      <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
      <TextInput
        id="password"
        v-model="form.password"
        type="password"
        placeholder="••••••••"
        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-white focus:border-transparent transition"
        :class="{ 'border-primary': form.passwordError }"
      />
      <p v-if="form.passwordError" class="text-primary text-xs mt-1">{{ form.passwordError }}</p>
    </div>

    <Button
      @click="handleLogin"
      :loading="form.isLoading"
      variant="primary"
      class="w-full px-6 py-3 rounded-lg text-white bg-gradient-to-r from-primary to-primary hover:from-primary-hover hover:to-from-primary-hover transition duration-300 shadow-md hover:shadow-lg"
    >
      Войти
    </Button>
  </div>
</template>
