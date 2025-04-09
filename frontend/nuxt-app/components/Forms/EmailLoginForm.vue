<script setup>
import { reactive, ref } from "vue";
import TextInput from "~/components/ui/Inputs/TextInput.vue";
import Button from "~/components/ui/Button/Button.vue";
import { useUserStore } from "~/stores/user";
import { emailRegex } from "~/shared/regexp";
const { user, isAuth, setUser } = useUserStore();

const isSend = ref(false);

const form = reactive({
  email: "asfahaikrkt6k56@yandex.ru",
  code: "",
  isLoading: false,
  emailError: "",
});

const validateEmail = () => {
  if (!form.email) {
    form.emailError = "Пожалуйста, введите email";
    return false;
  }

  const isValidEmail = emailRegex.test(form.email);

  if (!isValidEmail) {
    form.emailError = "Введите корректный email";
    return false;
  }

  form.emailError = "";
  return true;
};

const emit = defineEmits(["code-sent"]);

const authorize = async () => {
  try {
    form.isLoading = true;

    await useSanctumFetch("/sanctum/csrf-cookie", {
      method: "GET",
      credentials: "include",
    });

    const { data } = await useSanctumFetch("/api/auth/verify-code", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-XSRF-TOKEN": useCookie("XSRF-TOKEN").value,
      },
      credentials: "include",
      body: JSON.stringify({
        email: form.email,
        code: form.code,
      }),
    });

    // If verification succeeded, update user state
    if (data.value && data.value.status === "verified") {
      setUser(data.value.user);
      emit("close");
      emit("code-sent");
    }
  } catch (error) {
    console.error("Verification error:", error);
    form.emailError = "Ошибка при проверке кода. Попробуйте позже.";
  } finally {
    form.isLoading = false;
  }
};

const handleEmailLogin = async () => {
  try {
    form.isLoading = true;

    // Сначала получаем CSRF токен
    await useSanctumFetch("/sanctum/csrf-cookie", {
      method: "GET",
      credentials: "include",
    });

    // Затем делаем запрос
    const response = await useSanctumFetch("/api/auth/request-code", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-XSRF-TOKEN": useCookie("XSRF-TOKEN").value,
      },
      credentials: "include",
      body: JSON.stringify({
        email: form.email,
      }),
    });

    console.log(response);
    isSend.value = true;
  } catch (error) {
    console.error("Email login error:", error);
    form.emailError = "Ошибка при отправке кода. Попробуйте позже.";
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
        @keyup.enter="handleEmailLogin"
        @blur="validateEmail"
      />
      <p v-if="form.emailError" class="text-primary text-xs mt-1">{{ form.emailError }}</p>
    </div>

    <Button
      @click="handleEmailLogin"
      :disabled="form.isLoading || !!form.emailError"
      class="w-full px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-hover transition duration-300"
    >
      <span v-if="!form.isLoading">Получить код</span>
      <span v-else>Отправка...</span>
    </Button>

    <p class="text-xs text-gray-500 mt-2">На ваш email будет отправлен код подтверждения для входа</p>
    <div v-show="isSend" class="space-y-4">
      <div class="space-y-1">
        <label for="code" class="block text-sm font-medium text-gray-700">Код подтверждения</label>
        <TextInput
          id="code"
          v-model="form.code"
          type="code"
          placeholder="Код подтверждения"
          class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:bg-white focus:border-transparent transition"
          @keyup.enter="authorize"
        />
      </div>

      <Button
        @click="authorize"
        :disabled="form.isLoading"
        class="w-full px-6 py-3 rounded-lg bg-primary text-white hover:bg-primary-hover transition duration-300"
      >
        <span>Подтвердить</span>
      </Button>
    </div>
  </div>
</template>
