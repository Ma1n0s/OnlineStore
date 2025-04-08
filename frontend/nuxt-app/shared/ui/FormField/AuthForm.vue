<script setup>
import { ref, computed } from 'vue'
import LoginForm from './LoginForm.vue'
import PhoneLoginForm from './PhoneLoginForm.vue'
import RegisterForm from './RegisterForm.vue'

const authType = ref('login')
const isRegister = ref(false)

const currentFormComponent = computed(() => {
	if (isRegister.value) return RegisterForm
	return authType.value === 'login' ? LoginForm : PhoneLoginForm
})

const switchToRegister = () => {
	isRegister.value = true
}

const switchToLogin = () => {
	isRegister.value = false
}
</script>

<template>
	<div class="mx-auto p-8 bg-white rounded-2xl shadow-sm">
		<div v-if="!isRegister" class="relative mb-8 flex bg-gray-100 p-1 rounded-lg">
			<button
				@click="authType = 'login'"
				class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
				:class="{
					'text-primary bg-white rounded-md shadow-xs': authType === 'login',
					'text-gray-500 hover:text-gray-700': authType !== 'login',
				}"
			>
				Обычный вход
			</button>

			<button
				@click="authType = 'phone'"
				class="flex-1 py-2 px-4 text-sm font-medium text-center transition-colors"
				:class="{
					'text-primary bg-white rounded-md shadow-xs': authType === 'phone',
					'text-gray-500 hover:text-gray-700': authType !== 'phone',
				}"
			>
				Вход по коду
			</button>
		</div>

		<h2 v-if="isRegister" class="text-2xl font-bold text-center mb-6">Регистрация</h2>
		<h2 v-else class="text-2xl font-bold text-center mb-6">Вход в аккаунт</h2>

		<component :is="currentFormComponent" @switch-to-register="switchToRegister" @switch-to-login="switchToLogin" />
	</div>
</template>
