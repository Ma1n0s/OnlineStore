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
            <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Условия аренды</NuxtLink>
            <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Гарантия</NuxtLink>
            <NuxtLink to="/about" class="text-dark hover:text-primary font-bold text-sm">Оплата и доставка</NuxtLink>
          </div>

          <div class="flex items-center justify-between gap-4 xl:gap-8">
            <div class="flex flex-col items-start">
              <NuxtLink
                to="tel:+79169999999"
                class="text-primary font-bold text-nowrap text-xs xl:text-sm text-center flex items-center gap-1"
              >
                <Icon name="material-symbols:phone-android-rounded" class="h-4 w-4" />
                +7 (916) 999-99-99</NuxtLink
              >
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-center gap-1">
                <Icon name="material-symbols:nest-clock-farsight-analog-outline-rounded" class="h-4 w-4" />
                с 9:00 до 18:00 (Пн-Пт)
              </p>
            </div>

            <div>
              <p class="text-gray font-bold text-nowrap text-xs xl:text-sm flex items-start gap-1">
                <Icon name="material-symbols:location-on-rounded" class="h-4 w-4 mt-[2px] inline text-gray" />
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
            <NuxtImg src="full_logo_text.svg" class="h-[50px] w-fit" />
          </NuxtLink>

          <!-- <CatalogButton /> -->
          <div class="w-full max-w-[700px]">
            <Search />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="transparent" class="flex items-center gap-2 h-full !px-4" to="/account/cart-checkout"
            ><Icon name="material-symbols:shopping-cart-rounded" class="h-8 w-8 text-dark"
          /></Button>
          <Button v-if="isAuth" variant="transparent" class="flex items-center gap-2 h-full !px-4" @click="handleLogout"
            >{{ userDisplayName }} <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark"
          /></Button>
          <Button variant="transparent" class="flex items-center gap-2 h-full !px-4" @click="openModal" v-else>
            <Icon name="material-symbols:account-circle" class="h-8 w-8 text-dark"
          /></Button>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-zinc-200 flex justify-center w-full px-2 lg:hidden fixed z-50">
    <div class="flex justify-around flex-col md:flex-row items-center gap-4 pt-[2px] w-full max-w-screen-xl">
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
            <NuxtImg src="full_logo_text.svg" class="h-[40px] w-fit" />
          </NuxtLink>
        </div>

        <div class="flex items-center gap-2">
          <Button variant="transparent" class="flex items-center gap-2 h-full" @click="isMenuOpen = true"
            ><Icon name="material-symbols:search-rounded" class="h-8 w-8"
          /></Button>
          <Button variant="transparent" class="flex items-center gap-2 h-full" @click="menuOpen = true"
            ><Icon name="material-symbols:menu" class="h-8 w-8"
          /></Button>
        </div>
      </div>
    </div>
  </div>

  <div class="pb-[60px] lg:pb-[120px]"></div>

  <Modal class="min-h-[427px]" :isOpen="isModalOpen" @close="closeModal" @confirm="handleConfirm" title="">
    <AuthForm @close="closeModal" />
  </Modal>

  <Modal :isOpen="menuOpen" @close="closeMenu" @confirm="handleConfirm" title="">
    <div class="flex flex-col gap-4 max-h-[90vh] overflow-y-auto">
      <div>
        <NuxtLink to="/" class="flex items-center gap-2 w-[210px]">
          <NuxtImg src="full_logo_text.svg" class="h-[50px] w-fit" />
        </NuxtLink>
      </div>

      <Button v-if="isAuth" class="flex items-center gap-2 h-full" @click="handleLogout">
        <Icon name="solar:user-outline" class="h-8 w-8" />
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
        <Icon name="material-symbols:account-circle" class="h-8 w-8" />
        <span class="text-lg font-bold"> Вход </span>
      </Button>

      <Button to="/category" class="flex items-center gap-2 h-full" @click="closeMenu">
        <Icon name="material-symbols:view-list-rounded" class="h-8 w-8" />
        <span class="text-lg font-bold">Каталог</span>
      </Button>

      <Button class="flex items-center gap-2 h-full w-full" to="/account/cart-checkout" @click="closeMenu">
        <Icon name="material-symbols:shopping-cart-rounded" class="h-8 w-8" />
        <span class="text-lg font-bold">Корзина</span>
      </Button>

      <div class="flex flex-col gap-2">
        <Button to="/contacts" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:contact-phone-rounded" class="h-8 w-8" />
          <span class="text-lg font-bold">Контакты</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:move-location-rounded" class="h-8 w-8" />
          <span class="text-lg font-bold">О компании</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:passkey-rounded" class="h-8 w-8" />
          <span class="text-lg font-bold">Условия аренды</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:shield-rounded" class="h-8 w-8" />
          <span class="text-lg font-bold">Гарантия</span>
        </Button>
        <Button to="/about" class="flex items-center gap-2 h-full w-full" @click="closeMenu">
          <Icon name="material-symbols:local-shipping-rounded" class="h-8 w-8" />
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
              <Icon name="material-symbols:phone-android-rounded" class="h-4 w-4" />
              +7 (916) 999-99-99</NuxtLink
            >
            <p class="text-gray font-bold text-nowrap text-sm flex items-center gap-1">
              <Icon name="material-symbols:nest-clock-farsight-analog-outline-rounded" class="h-4 w-4" />
              с 9:00 до 18:00 (Пн-Пт)
            </p>
          </div>

          <div>
            <p class="text-gray font-bold text-nowrap text-sm flex items-start gap-1">
              <Icon name="material-symbols:location-on-rounded" class="h-4 w-4 mt-[2px] inline text-gray" />
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
// import CatalogButton from '../CatalogButton/CatalogButton.vue'
import Search from '../Search/Search.vue'
import Modal from '../Modal/Modal.vue'
import AuthForm from '../Forms/AuthForm.vue'

const menuOpen = ref(false)
const closeMenu = () => {
  menuOpen.value = false
}

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
