<template>
  <div
    class="bg-white hidden justify-center items-center flex-col w-full border-b border-blue-200 px-2 lg:flex fixed z-50"
  >
    <div class="flex justify-center items-center gap-2 w-full max-w-screen-2xl xl:px-8">
      <div class="flex items-center justify-around gap-2 w-full py-2">
        <div class="flex items-center gap-16 justify-start w-full">
          <NuxtLink to="/contacts" class="text-dark hover:text-primary font-bold text-sm">Контакты</NuxtLink>
          <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">О компании</NuxtLink>
          <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Условия аренды</NuxtLink>
          <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Гарантия</NuxtLink>
          <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Оплата и доставка</NuxtLink>
        </div>

        <div class="flex items-center justify-between gap-8">
          <div>
            <NuxtLink to="tel:+79169999999" class="text-primary font-bold text-nowrap text-sm"
              >+7 (916) 999-99-99</NuxtLink
            >
            <p class="text-gray font-bold text-nowrap text-sm">с 9:00 до 18:00 (Пн-Пт)</p>
          </div>

          <div>
            <p class="text-gray font-bold text-nowrap text-sm">
              Нижний Тагил, <br />
              ул. Аганичева 101а
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-around items-center gap-4 py-2 w-full max-w-screen-2xl xl:px-8">
      <div>
        <NuxtLink to="/" class="flex items-center gap-2 w-[210px]">
          <NuxtImg src="logo_test.svg" class="h-[50px] w-[50px]" />
          <p class="text-nowrap text-xl text-primary font-bold leading-4">Абсолют Техно</p>
        </NuxtLink>
      </div>

      <CatalogButton />
      <div class="w-full">
        <Search />
      </div>
      <Button class="flex items-center gap-2 h-full" to="/account/cart-checkout"
        ><Icon name="material-symbols:shopping-cart-rounded" class="h-6 w-6"
      /></Button>
      <Button v-if="isAuth" class="flex items-center gap-2 h-full" @click="handleLogout"
        >{{ userDisplayName }} <Icon name="solar:user-outline" class="h-6 w-6"
      /></Button>
      <Button class="flex items-center gap-2 h-full" @click="openModal" v-else
        ><span class="hidden lg:inline"> Вход </span> <Icon name="material-symbols:login-rounded" class="h-6 w-6"
      /></Button>
    </div>
  </div>

  <div class="bg-white flex justify-center w-full border-b border-blue-200 px-2 lg:hidden fixed z-50">
    <div class="flex justify-around flex-col md:flex-row items-center gap-4 py-2 w-full max-w-screen-xl">
      <div class="w-full h-full flex items-center gap-2" v-if="isMenuOpen">
        <Button class="flex items-center gap-2 h-full !px-2" @click="isMenuOpen = false"
          ><Icon name="material-symbols:close-rounded" class="h-6 w-6"
        /></Button>
        <div class="w-full">
          <Search @close="isMenuOpen = false" />
        </div>
      </div>

      <div class="flex items-center justify-between gap-4 w-full" v-else>
        <div>
          <NuxtLink to="/" class="flex items-center gap-2 w-full">
            <NuxtImg src="logo_test.svg" class="h-[40px] w-[40px]" />
            <p class="text-lg sm:text-xl text-primary font-bold ~w-[4em/8em] leading-5">Абсолют Техно</p>
          </NuxtLink>
        </div>

        <div class="flex items-center gap-2">
          <Button class="flex items-center gap-2 h-full" @click="isMenuOpen = true"
            ><Icon name="material-symbols:search-rounded" class="h-6 w-6"
          /></Button>
          <Button class="flex items-center gap-2 h-full"><Icon name="material-symbols:menu" class="h-6 w-6" /></Button>
        </div>
      </div>
    </div>
  </div>

  <div class="pb-[120px]"></div>

  <Modal class="min-h-[427px]" :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
    <AuthForm @close="closeModal" />
  </Modal>
</template>

<script setup lang="ts">
import Button from '~/components/ui/Button/Button.vue'
import { useUserStore } from '~/stores/user'
import CatalogButton from '../CatalogButton/CatalogButton.vue'
import Search from '../Search/Search.vue'
import Modal from '../Modal/Modal.vue'
import AuthForm from '../Forms/AuthForm.vue'

const userStore = useUserStore()
const { clearUser } = userStore

// Используем computed для доступа к реактивным свойствам store
const isAuth = computed(() => userStore.isAuth)
const user = computed(() => userStore.user)

// Вычисляемое свойство для отображения имени пользователя
const userDisplayName = computed(() => {
  if (!user.value) return 'Пользователь'
  return user.value.name || user.value.email || 'Пользователь'
})

const isMenuOpen = ref(false)

const isModalOpen = ref(false)

const openModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleConfirm = () => {
  closeModal()
}

const handleLogout = async () => {
  try {
    const { logout } = useSanctumAuth()
    await logout()
    clearUser()
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>
