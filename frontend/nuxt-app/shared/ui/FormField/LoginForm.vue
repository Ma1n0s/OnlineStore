<script setup>
import { reactive } from "vue";
import TextInput from "~/components/ui/Inputs/TextInput.vue";
import Button from "~/components/ui/Button/Button.vue";

const form = reactive({
  email: "",
  password: "",
  rememberMe: false,
  isLoading: false,
  emailError: "",
  passwordError: "",
});

const validate = () => {
  let valid = true;

  if (!form.email) {
    form.emailError = "Пожалуйста, введите email";
    valid = false;
  } else if (test(form.email)) {
    form.emailError = "Введите корректный email";
    valid = false;
  } else {
    form.emailError = "";
  }

  if (!form.password) {
    form.passwordError = "Пожалуйста, введите пароль";
    valid = false;
  } else if (form.password.length < 6) {
    form.passwordError = "Пароль должен быть не менее 6 символов";
    valid = false;
  } else {
    form.passwordError = "";
  }

  return valid;
};

const handleLogin = async () => {
  if (!validate()) return;

  try {
    form.isLoading = true;
  } catch (error) {
    console.error("Login error:", error);
  } finally {
    form.isLoading = false;
  }
};
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

    <div class="flex items-center justify-between">
      <label class="flex items-center">
        <input
          type="checkbox"
          v-model="form.rememberMe"
          class="h-4 w-4 text-primary focus:ring-primary-hover border-gray-300 rounded"
        />
        <span class="ml-2 text-sm text-gray-600">Запомнить меня</span>
      </label>
      <a href="#" class="text-sm text-primary hover:text-primary-active hover:underline">Забыли пароль?</a>
    </div>

    <Button
      @click="handleLogin"
      :loading="form.isLoading"
      variant="primary"
      class="w-full px-6 py-3 rounded-lg text-white bg-gradient-to-r from-primary to-primary hover:from-primary-hover hover:to-from-primary-hover transition duration-300 shadow-md hover:shadow-lg"
    >
      Войти
    </Button>

    <!-- <div class="text-center text-sm text-gray-500">
			Нет аккаунта?
			<a
				href="#"
				class="text-primary hover:text-primary-hover hover:underline"
				@click.prevent="$emit('switch-to-register')"
				>Зарегистрируйтесь</a
			>
		</div> -->
  </div>
</template>
