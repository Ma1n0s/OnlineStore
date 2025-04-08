<script setup>
import { reactive } from 'vue'
import TextInput from '~/components/ui/Inputs/TextInput.vue'
import Button from '~/components/ui/Button/Button.vue'

const form = reactive({
	email: '',
	isLoading: false,
	emailError: '',
})

const validateEmail = () => {
	if (!form.email) {
		form.emailError = 'Пожалуйста, введите email'
		return false
	}

	if (!re.test(form.email)) {
		form.emailError = 'Введите корректный email'
		return false
	}

	form.emailError = ''
	return true
}

const emit = defineEmits(['code-sent'])

const handleEmailLogin = async () => {
	if (!validateEmail()) return

	try {
		form.isLoading = true
	} catch (error) {
		console.error('Email login error:', error)
		form.emailError = 'Ошибка при отправке кода. Попробуйте позже.'
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
				@keyup.enter="handleEmailLogin"
			/>
			<p v-if="form.emailError" class="text-primary text-xs mt-1">{{ form.emailError }}</p>
		</div>

		<Button
			@click="handleEmailLogin"
			:disabled="form.isLoading"
			class="w-full px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-hover transition duration-300"
		>
			<span v-if="!form.isLoading">Получить код</span>
			<span v-else>Отправка...</span>
		</Button>

		<p class="text-xs text-gray-500 mt-2">На ваш email будет отправлен код подтверждения для входа</p>
	</div>
</template>
