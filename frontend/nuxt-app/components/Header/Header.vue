<template>
  <div class="fixed z-50 w-full">
    <div
      class="bg-zinc-200 justify-center items-center flex-col w-full lg:flex transition-all duration-300"
      :class="{ 'opacity-0 h-0 overflow-hidden': isScrolled }"
    >
      <div class="flex justify-center items-center gap-1 xl:gap-2 w-full max-w-screen-2xl px-8">
        <div class="flex items-center justify-around gap-2 w-full py-2">
          <div class="flex items-center gap-8 xl:gap-16 justify-start w-full">
            <NuxtLink to="/contacts" class="text-dark hover:text-primary font-bold text-sm">Контакты</NuxtLink>
            <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">О компании</NuxtLink>
            <NuxtLink to="/news/3" class="text-dark hover:text-primary font-bold text-sm">Условия аренды</NuxtLink>
            <NuxtLink to="/news/1" class="text-dark hover:text-primary font-bold text-sm">Гарантия</NuxtLink>
            <NuxtLink to="/news/5" class="text-dark hover:text-primary font-bold text-sm">Оплата и доставка</NuxtLink>
          </div>

          <div class="flex items-center justify-between gap-4 xl:gap-8">
            <div class="flex flex-col items-start">
              <NuxtLink
                to="tel:+79169999999"
                class="text-primary font-bold text-nowrap text-xs xl:text-sm text-center flex items-center gap-1"
              >
                <Icon name="material-symbols:phone-android-rounded" class="h-4 w-4" alt="Телефон" />
                +7 (916) 999-99-99</NuxtLink
              >
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-center gap-1">
                <Icon
                  name="material-symbols:nest-clock-farsight-analog-outline-rounded"
                  class="h-4 w-4"
                  alt="Время работы"
                />
                с 9:00 до 18:00 (Пн-Пт)
              </p>
            </div>

            <div>
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-start gap-1">
                <Icon
                  name="material-symbols:location-on-rounded"
                  class="h-4 w-4 mt-[2px] inline text-gray"
                  alt="Адрес"
                />
                Нижний Тагил, <br />
                ул. Аганичева 101а
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      class="bg-zinc-200 hidden lg:flex justify-center items-center gap-4 py-2 w-full transition-all duration-300"
      :class="{ '!py-1': isScrolled }"
    >
      <div class="flex justify-between gap-2 items-center w-full max-w-screen-2xl px-8">
        <div class="flex items-center gap-2 w-full">
          <NuxtLink to="/" class="flex items-center gap-2 w-[210px]">
            <NuxtImg src="full_logo_text.svg" class="h-[50px] w-fit" alt="Абсолют Техно" />
          </NuxtLink>

          <!-- <CatalogButton /> -->
          <div class="w-full max-w-[700px]">
            <Search />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <Button
            variant="transparent"
            class="flex items-center gap-2 h-full !px-4 relative"
            to="/account/cart-checkout"
            ><Icon name="material-symbols:shopping-cart-rounded" class="h-8 w-12 text-dark" alt="Корзина" />
            <span
              v-if="!!products.length"
              class="absolute top-0 right-0 text-primary z-50 font-bold"
              :class="products.length > 99 ? 'right-0' : 'right-[0.4rem]'"
              >{{ products.length }}</span
            ></Button
          >

          <div class="relative w-full h-full">
            <Button
              v-if="isAuth"
              variant="transparent"
              class="flex items-center gap-2 h-full !px-4"
              @click="miniMenu = true"
              >{{ userDisplayName }}
              <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark" alt="Пользователь" />
            </Button>
            <Button variant="transparent" class="flex items-center gap-2 h-full !px-4" @click="openModal" v-else>
              <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark" alt="Вход"
            /></Button>

            <Menu v-if="miniMenu" v-model="miniMenu" />
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-zinc-200 flex justify-center w-full px-2 lg:hidden fixed z-50">
    <div class="flex justify-around flex-col md:flex-row items-center gap-4 pt-[2px] w-full max-w-screen-xl">
      <div class="w-full h-full flex items-center gap-2" v-if="isMenuOpen">
        <div class="w-full">
          <Search @close="isMenuOpen = false" />
        </div>
        <Button variant="transparent" class="flex items-center gap-2 h-full" @click="isMenuOpen = false"
          ><Icon name="material-symbols:close-rounded" class="h-8 w-8 text-danger" alt="Закрыть"
        /></Button>
      </div>

      <div class="flex items-center justify-between gap-4 w-full" v-else>
        <div>
          <NuxtLink to="/" class="flex items-center gap-2 w-full">
            <NuxtImg src="full_logo_text.svg" class="h-[40px] w-fit" alt="Абсолют Техно" />
          </NuxtLink>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="transparent" class="flex items-center gap-2 h-full" @click="isMenuOpen = true"
            ><Icon name="material-symbols:search-rounded" class="h-8 w-8" alt="Поиск"
          /></Button>
        </div>
      </div>
    </div>
  </div>

  <div class="pb-[65px] lg:pb-[140px]"></div>

  <Modal class="min-h-[427px]" :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
    <AuthForm @close="closeModal" />
  </Modal>

  <Modal :isOpen="menuOpen" @close="closeMenu" @confirm="handleConfirm" title="">
    <div class="flex flex-col gap-4">
      <div>
        <NuxtLink to="/" class="flex items-center gap-2 w-[210px]">
          <NuxtImg src="full_logo_text.svg" class="h-[50px] w-fit" alt="Абсолют Техно" />
        </NuxtLink>
      </div>

      <Button v-if="isAuth" class="flex items-center gap-2 h-full relative" @click="handleLogout">
        <Icon name="solar:user-outline" class="h-8 w-8" alt="Пользователь" />
        <span class="text-lg font-bold">{{ userDisplayName }}</span>
      </Button>

      <Button
        class="flex items-center gap-2 h-full w-full"
        @click="
          () => {
            closeMenu()
            openModal()
          }
        "
        v-else
      >
        <Icon name="material-symbols:account-circle" class="h-8 w-8" alt="Вход" />
        <span class="text-lg font-bold"> Вход </span>
      </Button>

      <Button to="/category" class="flex items-center gap-2 h-full" @click="closeMenu">
        <Icon name="material-symbols:view-list-rounded" class="h-8 w-8" alt="Каталог" />
        <span class="text-lg font-bold">Каталог</span>
      </Button>

      <Button class="flex items-center gap-2 h-full w-full" to="/account/cart-checkout" @click="closeMenu">
        <Icon name="material-symbols:shopping-cart-rounded" class="h-8 w-8" alt="Корзина" />
        <span class="text-lg font-bold">Корзина</span>
      </Button>

      <div class="flex flex-col gap-2">
        <Button to="/contacts" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:contact-phone-rounded" class="h-8 w-8" alt="Контакты" />
          <span class="text-lg font-bold">Контакты</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:move-location-rounded" class="h-8 w-8" alt="О компании" />
          <span class="text-lg font-bold">О компании</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:passkey-rounded" class="h-8 w-8" alt="Условия аренды" />
          <span class="text-lg font-bold">Условия аренды</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:shield-rounded" class="h-8 w-8" alt="Гарантия" />
          <span class="text-lg font-bold">Гарантия</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:local-shipping-rounded" class="h-8 w-8" alt="Оплата и доставка" />
          <span class="text-lg font-bold">Оплата и доставка</span>
        </Button>
      </div>

      <div class="flex items-center justify-around gap-2 w-full py-2">
        <div class="flex flex-wrap items-center justify-center sm:justify-between gap-8 w-full">
          <div class="flex flex-col items-start">
            <NuxtLink
              to="tel:+79169999999"
              class="text-primary font-bold text-nowrap text-sm text-center flex items-center gap-1"
            >
              <Icon name="material-symbols:phone-android-rounded" class="h-4 w-4" alt="Телефон" />
              +7 (916) 999-99-99</NuxtLink
            >
            <p class="text-gray font-bold text-nowrap text-sm flex items-center gap-1">
              <Icon
                name="material-symbols:nest-clock-farsight-analog-outline-rounded"
                class="h-4 w-4"
                alt="Время работы"
              />
              с 9:00 до 18:00 (Пн-Пт)
            </p>
          </div>

          <div>
            <p class="text-gray font-bold text-nowrap text-sm flex items-start gap-1">
              <Icon name="material-symbols:location-on-rounded" class="h-4 w-4 mt-[2px] inline text-gray" alt="Адрес" />
              Нижний Тагил, <br />
              ул. Аганичева 101а
            </p>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import Button from '~/components/ui/Button/Button.vue'
import { useUserStore } from '~/stores/user'
import Search from '../Search/Search.vue'
import Modal from '../Modal/Modal.vue'
import Menu from './Menu.vue'
import AuthForm from '../Forms/AuthForm.vue'
import { useCartStore } from '~/stores/cart'
import { storeToRefs } from 'pinia'

const cartStore = useCartStore()
const { products } = storeToRefs(cartStore)

const menuOpen = ref(false)
const closeMenu = () => {
  menuOpen.value = false
}

const userStore = useUserStore()
const { clearUser } = userStore
const { showAuthForm: isModalOpen } = storeToRefs(userStore)

// Используем computed для доступа к реактивным свойствам store
const isAuth = computed(() => userStore.isAuth)
const user = computed(() => userStore.user)

// Вычисляемое свойство для отображения имени пользователя
const userDisplayName = computed(() => {
  if (!user.value) return 'Пользователь'
  return user.value.name || user.value.email || 'Пользователь'
})

const miniMenu = ref(false)

const isMenuOpen = ref(false)

// const isModalOpen = ref(false)

const isScrolled = ref(false)

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

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

<style scoped>
.opacity-0 {
  opacity: 0;
}
</style>
