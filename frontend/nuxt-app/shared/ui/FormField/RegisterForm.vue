<script setup>
import { reactive, ref } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'

const form = reactive({
	name: '',
	email: '',
	password: '',
	confirmPassword: '',
	isLoading: false,
	errors: {
		name: '',
		email: '',
		password: '',
		confirmPassword: '',
	},
})

const isConfirmationStep = ref(false)
const confirmationCode = ref('')
const confirmationError = ref('')

const validate = () => {
	let valid = true

	// Reset errors
	form.errors = {
		name: '',
		email: '',
		password: '',
		confirmPassword: '',
	}

	if (!form.name.trim()) {
		form.errors.name = 'Пожалуйста, введите имя'
		valid = false
	}

	if (!form.email) {
		form.errors.email = 'Пожалуйста, введите email'
		valid = false
	} else if (!/^\S+@\S+\.\S+$/.test(form.email)) {
		form.errors.email = 'Введите корректный email'
		valid = false
	}

	if (!form.password) {
		form.errors.password = 'Пожалуйста, введите пароль'
		valid = false
	} else if (form.password.length < 6) {
		form.errors.password = 'Пароль должен быть не менее 6 символов'
		valid = false
	}

	if (form.password !== form.confirmPassword) {
		form.errors.confirmPassword = 'Пароли не совпадают'
		valid = false
	}

	return valid
}

const handleRegister = async () => {
	if (!validate()) return

	try {
		form.isLoading = true
		// Здесь должна быть логика отправки данных на сервер
		// После успешной отправки переключаем на шаг подтверждения
		isConfirmationStep.value = true
	} catch (error) {
		console.error('Registration error:', error)
	} finally {
		form.isLoading = false
	}
}

const handleConfirmation = async () => {
	if (!confirmationCode.value) {
		confirmationError.value = 'Пожалуйста, введите код подтверждения'
		return
	}

	try {
		form.isLoading = true
		// Здесь должна быть логика подтверждения кода
		// После успешного подтверждения можно закрыть модальное окно или перенаправить пользователя
		console.log('Registration successful!')
	} catch (error) {
		confirmationError.value = 'Неверный код подтверждения'
		console.error('Confirmation error:', error)
	} finally {
		form.isLoading = false
	}
}

const backToRegistration = () => {
	isConfirmationStep.value = false
	confirmationCode.value = ''
	confirmationError.value = ''
}
</script>

<template>
	<div class="w-full space-y-4">
		<!-- Шаг регистрации -->
		<div v-if="!isConfirmationStep" class="space-y-4">
			<div class="space-y-1">
				<label for="name" class="block text-sm font-medium text-gray-700">Имя</label>
				<TextInput
					id="name"
					v-model="form.name"
					type="text"
					placeholder="Ваше имя"
					class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:bg-white focus:border-transparent transition"
					:class="{ 'border-primary': form.errors.name }"
				/>
				<p v-if="form.errors.name" class="text-primary text-xs mt-1">{{ form.errors.name }}</p>
			</div>

			<div class="space-y-1">
				<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
				<TextInput
					id="email"
					v-model="form.email"
					type="email"
					placeholder="example@mail.com"
					class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:bg-white focus:border-transparent transition"
					:class="{ 'border-primary': form.errors.email }"
				/>
				<p v-if="form.errors.email" class="text-primary text-xs mt-1">{{ form.errors.email }}</p>
			</div>

			<div class="space-y-1">
				<label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
				<TextInput
					id="password"
					v-model="form.password"
					type="password"
					placeholder="••••••••"
					class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-white focus:border-transparent transition"
					:class="{ 'border-primary': form.errors.password }"
				/>
				<p v-if="form.errors.password" class="text-primary text-xs mt-1">{{ form.errors.password }}</p>
			</div>

			<div class="space-y-1">
				<label for="confirmPassword" class="block text-sm font-medium text-gray-700">Подтвердите пароль</label>
				<TextInput
					id="confirmPassword"
					v-model="form.confirmPassword"
					type="password"
					placeholder="••••••••"
					class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-white focus:border-transparent transition"
					:class="{ 'border-primary': form.errors.confirmPassword }"
				/>
				<p v-if="form.errors.confirmPassword" class="text-primary text-xs mt-1">{{ form.errors.confirmPassword }}</p>
			</div>

			<Button
				@click="handleRegister"
				:loading="form.isLoading"
				variant="primary"
				class="w-full px-6 py-3 rounded-lg text-white bg-gradient-to-r from-primary to-primary hover:from-primary-hover hover:to-from-primary-hover transition duration-300 shadow-md hover:shadow-lg"
			>
				Зарегистрироваться
			</Button>

			<div class="text-center text-sm text-gray-500">
				Уже есть аккаунт?
				<a
					href="#"
					class="text-primary hover:text-primary-hover hover:underline"
					@click.prevent="$emit('switch-to-login')"
					>Войдите</a
				>
			</div>
		</div>

		<!-- Шаг подтверждения -->
		<div v-else class="space-y-4">
			<div class="text-center">
				<h3 class="text-lg font-semibold text-gray-800">Подтвердите email</h3>
				<p class="text-sm text-gray-600 mt-2">
					Мы отправили код подтверждения на <span class="font-medium">{{ form.email }}</span>
				</p>
			</div>

			<div class="space-y-1">
				<label for="confirmationCode" class="block text-sm font-medium text-gray-700">Код подтверждения</label>
				<TextInput
					id="confirmationCode"
					v-model="confirmationCode"
					type="text"
					placeholder="Введите 6-значный код"
					class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:bg-white focus:border-transparent transition text-center tracking-widest"
					:class="{ 'border-primary': confirmationError }"
					maxlength="6"
				/>
				<p v-if="confirmationError" class="text-primary text-xs mt-1">{{ confirmationError }}</p>
			</div>

			<Button
				@click="handleConfirmation"
				:loading="form.isLoading"
				variant="primary"
				class="w-full px-6 py-3 rounded-lg text-white bg-gradient-to-r from-primary to-primary hover:from-primary-hover hover:to-from-primary-hover transition duration-300 shadow-md hover:shadow-lg"
			>
				Подтвердить
			</Button>

			<div class="text-center text-sm text-gray-500">
				Не получили код?
				<button @click="backToRegistration" class="text-primary hover:text-primary-hover hover:underline">
					Отправить снова
				</button>
			</div>
		</div>
	</div>
</template>
