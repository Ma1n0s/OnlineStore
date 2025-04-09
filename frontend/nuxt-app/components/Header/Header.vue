<template>
  <div class="bg-white flex justify-center w-full border-b border-blue-200 px-2">
    <div class="flex justify-around items-center gap-4 py-2 w-full max-w-screen-xl">
      <NuxtLink to="/" class="flex items-center gap-2 w-[400px]">
        <NuxtImg src="logo_test.svg" width="40" height="40" />
        <div class="text-nowrap text-xl text-primary font-bold">Абсолют Техно</div>
      </NuxtLink>
      <CatalogButton />
      <Search />
      <Button class="flex items-center gap-2 h-full"
        ><Icon name="material-symbols:shopping-cart-rounded" class="h-6 w-6"
      /></Button>
      <Button v-if="isAuth" class="flex items-center gap-2 h-full" @click="handleLogout"
        >{{ userDisplayName }} <Icon name="solar:user-outline" class="h-6 w-6"
      /></Button>
      <Button class="flex items-center gap-2 h-full" @click="openModal" v-else
        >Войти <Icon name="material-symbols:login-rounded" class="h-6 w-6"
      /></Button>
    </div>
  </div>
  <Modal class="min-h-[427px]" :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
    <AuthForm />
  </Modal>
</template>

<script setup lang="ts">
import Button from "~/components/ui/Button/Button.vue";
import { useUserStore } from "~/stores/user";
import CatalogButton from "../CatalogButton/CatalogButton.vue";
import Search from "../Search/Search.vue";
import Modal from "../Modal/Modal.vue";
import AuthForm from "../Forms/AuthForm.vue";

const userStore = useUserStore();
const { clearUser } = userStore;

// Используем computed для доступа к реактивным свойствам store
const isAuth = computed(() => userStore.isAuth);
const user = computed(() => userStore.user);

// Вычисляемое свойство для отображения имени пользователя
const userDisplayName = computed(() => {
  if (!user.value) return "Пользователь";
  return user.value.name || user.value.email || "Пользователь";
});

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const handleConfirm = () => {
  closeModal();
};

const handleLogout = async () => {
  try {
    const { logout } = useSanctumAuth();
    await logout();

    // Очищаем данные пользователя в store
    clearUser();
  } catch (error) {
    console.error("Logout error:", error);
  }
};
</script>
